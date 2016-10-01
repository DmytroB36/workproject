<?php
/**
* Полностью статичный класс.
* Библиотека для безопасного обращения к БД
*/
  namespace Framework;

  class db{

      private static $linkDb;
      private static $lastError;

      /**
      * Соединение с базой данных по переданным параметрам
      *
      * @param mixed $host
      * @param mixed $user
      * @param mixed $pass
      * @param mixed $dbname
      */
      private static function _connect($host, $user, $pass, $dbname){
        if(self::isConnected()){
            return true;
        }
        $res = mysqli_connect($host, $user, $pass, $dbname);
        if(is_object($res) && $res->select_db($dbname)){
            $res->set_charset(\App\App::$config->get('dbCharset'));
            self::$linkDb = $res;
            return true;
        }else{
            throw new Exception("Невозможно подключиться к БД");
        }
      }

      public static function disconnect(){
        $res = null;
        if(self::isConnected()){
            $res = mysqli_close(self::$linkDb);
        }
        self::$linkDb = null;
        return $res;
      }

      private static function isConnected(){
          return is_object(self::$linkDb);
      }

      /**
      * Установка соединения с параметрами из конфигурации приложения
      *
      */
      public static function connect(){
        if(self::isConnected()){
            return self::$linkDb;
        }
        return self::_connect(
            \App\App::$config->get('dbHost'),
            \App\App::$config->get('dbUser'),
            \App\App::$config->get('dbPass'),
            \App\App::$config->get('dbName')
        );
      }

      /**
      * Выполнение запроса к БД
      *
      * @return resource
      */
      public static function query(){
          self::connect();
          return self::rawQuery(self::prepareQuery(func_get_args()));
      }


      private static function rawQuery($query){
        $res   = mysqli_query(self::$linkDb, $query);
        if( ! $res){
            $error = mysqli_error(self::$linkDb);
            self::$lastError = "$error. Full query: [$query]";
        }
        return $res;
      }

      /**
      * Обрадотка строки запроса на предмет наличия плейсхолдеров и
      * из безопасная обработка
      *
      * @param mixed $args
      */
      private static function prepareQuery($args){
        $query = '';
        $raw   = array_shift($args);
        /** разбиваем строку запроса на части плейсхолдеры и чистый sql по очереди*/
        $array = preg_split('~(\?[nsiuap])~u',$raw,null,PREG_SPLIT_DELIM_CAPTURE);
        $anum  = count($args); // кол-во аргументов
        $pnum  = floor(count($array) / 2); // кол-во плейсхолдеров
        if( $pnum != $anum ){
            self::$lastError = "Number of args ($anum) doesn't match number of placeholders ($pnum) in [$raw]";
        }
        foreach($array as $i => $part){
            if( ($i % 2) == 0 ){
                $query .= $part;
                continue;
            }
            $value = array_shift($args);
            switch ($part){
                case '?n':
                    $part = self::escapeIdent($value);
                    break;
                case '?s':
                    $part = self::escapeString($value);
                    break;
                case '?i':
                    $part = self::escapeInt($value);
                    break;
                case '?a':
                    $part = self::createIN($value);
                    break;
                case '?u':
                    $part = self::createSET($value);
                    break;
                case '?p':
                    $part = $value;
                    break;
            }
            $query .= $part;
        }
        return $query;
    }

    private static function escapeInt($value){
        if ($value === NULL){
            return 'NULL';
        }
        if( ! is_numeric($value)){
            self::$lastError = "Integer (?i) placeholder expects numeric value, ".gettype($value)." given";
            return false;
        }
        if(is_float($value)){
            $value = number_format($value, 0, '.', '');
        }
        return $value;
    }

    private static function escapeString($value){
        if ($value === NULL){
            return 'NULL';
        }
        return "'".mysqli_real_escape_string(self::$linkDb, $value)."'";
    }

    private static function escapeIdent($value){
        if($value){
            return "`".str_replace("`","``",$value)."`";
        }else{
             self::$lastError = "Empty value for identifier (?n) placeholder";
        }
    }

    private static function createIN($data){
        if( ! is_array($data)){
            self::$lastError = "Value for IN (?a) placeholder should be array";
            return;
        }
        if ( ! $data){
            return 'NULL';
        }
        $query = $comma = '';
        foreach ($data as $value){
            $query .= $comma.self::escapeString($value);
            $comma  = ",";
        }
        return $query;
    }

    private static function createSET($data){
        if( ! is_array($data)){
            self::$lastError = "SET (?u) placeholder expects array, ".gettype($data)." given";
            return;
        }
        if( ! $data){
            self::$lastError = "Empty array for SET (?u) placeholder";
            return;
        }
        $query = $comma = '';
        foreach ($data as $key => $value){
            $query .= $comma.self::escapeIdent($key).'='.self::escapeString($value);
            $comma  = ",";
        }
        return $query;
    }

    /**
    * id последней добавленной записи
    * @return int
    */
    public static function lastInsertId(){
        return mysqli_insert_id(self::$linkDb);
    }

    /**
    * Кол-во затронутых записей в последнем запросе.
    * @return int
    */
    public static function affectedRows(){
        return mysqli_affected_rows(self::$linkDb);
    }

    /**
    * Получение строки из результат выполнения запроса
    *
    * @param resource $result
    * @return array
    */
    public static function fetch($result){
        $res = mysqli_fetch_assoc($result);
        if(is_null($res)){
          self::clear($result);
          return false;
        }
        return $res;
    }

    /**
    * Получение полного результата выполнения запроса
    *
    * @param resource $result
    * @return array
    */
    public static function fetchAll($result){
        $res = [];
        while($row = self::fetch($result)){
            array_push($res, $row);
        }
        return $res;
    }

    /**
    * Получение скалярного значения из результат выполнения запроса
    *
    * @param resource $result
    * @return scalar value
    */
    public static function fetchScalar($result) {
        $res = self::fetch($result);
        if(is_array($res)) {
          return reset($res);
        }else{
          return $res;
        }
    }

    public static function clear($result){
        if(is_object($result) && empty($result->cleared)){
          $result->cleared = true;
          return mysqli_free_result($result);
        }
    return false;
  }

  }

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
      * Хранятся параметры запроса для передачи в колбак ф-ю
      *
      * @var array
      */
      private static $queryParams = [];

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
      public static function query($sql, $params){
          self::connect();
          return self::rawQuery(self::prepareQuery($sql, $params));
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
      * Обработка параметров переданных в строке запроса
      *
      * @param mixed $matches
      */
      private static function grabParams($matches){
          /*
            $matches[0] - всё вхождение
            $matches[1] - откр. кавычка или пусто
            $matches[2] - двоеточие
            $matches[3] - название параметра
            $matches[4] - закр. кавычка или пусто
          */
          if( ! isset(self::$queryParams[ $matches[3] ])){
              throw new \Exception( "Отсутствует один из параметров переданных в запрос" );
          }
          $value = self::$queryParams[ $matches[3] ];
          if(empty($matches[1])){
            $value = self::escapeNumber($value);
          }else{
            $value = self::escapeString($value);
          }
          return $value;
      }

      /**
      * Обрадотка строки запроса на предмет наличия плейсхолдеров и
      * из безопасная обработка
      *
      * @param mixed $args
      */
      private static function prepareQuery($sql, $params){
        $query = '';
        $raw   = $sql;
        self::$queryParams = $params;
        /*
            находим параметры видов
            :param1 - обрабатывать как число
            ':param1' - обрабатывать как строку
        */
        $sqlSafe = preg_replace_callback('/(\'?)(\:)([a-zA-Z0-9_]+)(\'?)/', [__CLASS__, 'grabParams'], $sql);
        return $sqlSafe;
    }

      /**
      * Обработка числового параметра
      *
      * @param int|float|array $value
      * @return string|int|float;
      */
      private static function escapeNumber($value){
        if(is_array($value)){
            /* через запятую для IN */
            $res = [];
            foreach ($value as $val){
                $res[] = self::escapeNumber($val);
            }
            if( ! empty($res)){
                return implode(',', $res);
            }
            return 'null';
        }elseif(is_bool($value)){
            return (int)$value;
        }elseif(preg_match('/^\-*\d+(\.\d+)?$/', $value, $matches)){
            /* вместо is_numeric гер. выражение чтобы не проходили числа в других системах счисления */
            return $matches[0];
        }
        return 'null';
      }

      /**
      * Обработка строкового параметра
      *
      * @param string|array $value
      * @return string;
      */
      private static function escapeString($value){
        if(is_array($value)){
            /* через запятую для IN */
            $res = [];
            foreach ($value as $val){
                $res[] = self::escapeString($val);
            }
            if( ! empty($res)){
                return implode(',', $res);
            }
            return 'null';
        }else{
            $res = "'".mysqli_real_escape_string(self::$linkDb, $value)."'";
            return $res;
        }
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

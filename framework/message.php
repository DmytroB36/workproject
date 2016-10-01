<?php

/**
* Объект куда можно ложить сообщения разных типов.
* чтобы хранить сообщения юзера до получения из пользователе,
* а не до завершения работы скрипта,
* предпочел бы ипользовать memcached
*
*/
  namespace Framework;

  class message{

      /** Информационные сообщения об успехе */
      const TYPE_INFO = 1;

      /** Сообщения об ошибках пользователя */
      const TYPE_WARNING = 2;

      /** Сообщения о серьезных ошибках */
      const TYPE_ERROR = 3;

      private $data;

      /**
      * Добавить новое сообщение выбранного типа
      *
      * @param int $type TYPE_INFO | TYPE_WARNING | TYPE_ERROR
      * @param mixed $text
      */
      public function push($type, $text){
          if( ! isset($this->data[$type])){
              $this->data[$type] = array();
          }
          array_push($this->data[$type], $text);
      }

      /**
      * Добавить сразу несколько сообщений одного типа
      *
      * @param int $type TYPE_INFO | TYPE_WARNING | TYPE_ERROR
      * @param array $messageList
      */
      public function pushList($type, $messageList){

          foreach($messageList as $value) {
            $this->push($type, $value);
          }
      }

      /**
      * Вычитать сообщение с его удалением
      *
      * @param int $type TYPE_INFO | TYPE_WARNING | TYPE_ERROR
      * @return string
      */
      public function pop($type){
          if(empty($this->data[$type]) || !is_array($this->data[$type])){
              return null;
          }
          return array_pop($this->data[$type]);
      }

  }

<?php
/**
* Родительский класс для классов конфигурации
*/
  namespace Framework;

  abstract class config{

      protected $options = [];

      /**
      * put your comment there...
      *
      * @param mixed $key - название опции
      * @return mixed значение опции
      */
      public function get($key){
          if( ! isset($this->options[ $key ])){
              return null;
          }
          return $this->options[$key];
      }

  }


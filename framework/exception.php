<?php
/**
* Внутренние исключения фреймворка
*/
  namespace Framework;

  class Exception extends \Exception{

      const TEMPLATE_FILE_NOT_FOUND = 1;

      const CONTROLLER_CLASS_NOT_FOUND = 2;

      const CONTROLLER_ACTION_NOT_FOUND = 3;

      /**
      * При генерации указываем только код как константу этого класса
      * Заготовки текста будут подставлены автоматически
      *
      * @param mixed $code
      * @return Exception
      */
      public function __construct($code){
          $message = self::getMessageByCode($code);
          return parent::__construct($message, $code);
      }

      private static function getMessageByCode($code){
          switch($code){
            case self::TEMPLATE_FILE_NOT_FOUND:
                $message = "Не найден файл шаблона";
                break;
            case self::CONTROLLER_CLASS_NOT_FOUND:
                $message = "Класс контроллера не найден";
                break;
            case self::CONTROLLER_ACTION_NOT_FOUND:
                $message = "Указанное действие не досутпно в этом контроллере";
                break;
            default:
                $message = 'UNKNOWN ERROR';
                break;
          }
          return $message;

      }

  }

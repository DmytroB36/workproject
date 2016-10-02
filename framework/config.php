<?php
/**
* Родительский класс для классов конфигурации
*/
  namespace Framework;

  abstract class config{

      protected $options = [];

      /**
      * Путь к файлам конфигурации
      *
      * @var string
      */
      protected $configFilePath = '';


      /**
      * Путь к конфигурационным файлам
      *
      * @param string $path
      */
      public function __construct($path){
        $this->configFilePath = $path;
      }

      /**
      * Формат [configsection]_[config_key]
      * Пример: db_pass()
      *
      * @param mixed $name
      * @param mixed $args
      * @return mixed
      */
      public function __call($name, $args){
        $tmp = explode('_', $name, 2);
        $cfgSection = strtolower($tmp[0]);
        $cfgKey = strtolower($tmp[1]);
        if(empty($cfgSection) || empty($cfgKey)){
            return null;
        }
        return $this->_get($cfgSection, $cfgKey);
      }

      /**
      * Читает и возвращает содержимое фвайла конфигурации
      *
      * @param string $section
      * @return string
      */
      protected function getConfigFile($section){
        return file_get_contents($this->configFilePath.'/config_'.$section.'.json');
      }

      /**
      * Значение опции
      *
      * @param string $section
      * @param string $key
      * @return mixed
      */
      protected function _get($section, $key){
        if(empty($this->options[ $section ])){
            $this->options[$section] = $this->parseConfigFile($section);
        }
        if( ! isset($this->options[$section][$key])){
            return null;
        }
        return $this->options[$section][$key];
      }

      /**
      * Разбирает прочитанные из файла данные
      *
      * @param string $section
      * @return array
      */
      protected function parseConfigFile($section){
        return json_decode($this->getConfigFile($section), true);
      }

  }


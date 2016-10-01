<?php
/**
* Автозагрузчик файлов классов.
* Путь к файлу класса должен совпадать с пространством имен искомого класса
*/
namespace Framework;

class Loader{

    public function __construct(){
        spl_autoload_register( [$this, 'loader'] );
    }

    private static function getRealPath($className){
        $className = str_replace('\\', '/', $className);
        return \__ROOT__.'/'.$className.'.php';
    }

    private function loader($className){
        $className = strtolower($className);
        $file = self::getRealPath($className);
        if(file_exists( $file )){
            require_once($file);
            return;
        }
    }

}

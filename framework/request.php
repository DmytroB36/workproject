<?php

/**
* Объект работы с параметрами от клиента
*/
namespace Framework;

class request{

    public function __construct(){
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    /**
    * Получить значение переменной из одного из массивов
    * приоритет: GET
    *
    * @param mixed $key
    * @param mixed $default
    */
    public function get($key, $default){
        $data = null;
        if(isset($this->get[$key])){
            $data = $this->get[$key];
        }elseif(isset($this->post[$key])){
            $data = $this->post[$key];
        }
        return $data;
    }

    /**
    * Получить значение из массива $_SERVER
    *
    * @param mixed $key
    */
    public function server($key){
        return $this->server[$key];
    }

}

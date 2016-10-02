<?php
/**
* Объект реестра.
* Может использоваться для хранения данных необходимых в разных местах
* приложения
*/
namespace Framework;

class Registry implements \ArrayAccess{

    private $data = [];

    /**
    * Установить пару ключ-значение
    *
    * @param mixed $key
    * @param mixed $val
    */
    public function set($key, $val){
        if( ! isset($this->data[$key])){
            $this->data[$key] = $val;
            return true;
        }else{
            throw new \Framework\Exception("Попытка перезаписать значение переменно ".$key);
        }

    }

    /**
    * Получить значение ключа
    *
    * @param mixed $key
    */
    public function get($key){
        if( ! isset($this->data[$key])){
            return null;
        }
        return $this->data[$key];
    }

    /**
    * Удалить ключ
    *
    * @param mixed $key
    */
    public function clear($key){
        unset($this->data[$key]);
    }

    /*  методы для реализации ArrayAccess  */


    public function offsetExists($offset) {
         return isset($this->data[$offset]);
     }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }


}


<?php
/**
*   Объект формирования и отдачи контента клиенту
*/
namespace Framework;

class Response{
    const CODE_NOT_FOUND = 404;

    /** Тело ответа */
    private $body;

    /** Код */
    private $code;

    /** Заголовки */
    private $headers = array();

    public function __construct($body=null){
        $this->body = $body;
     }

    /**
    * Добавить заголовок
    *
    * @param string $name
    * @param string $value
    */
    public function setHeader($name, $value=null){
        if(isset($this->headers[$name])){
            /* если такой заголовок существует, добавляем новое значение
               через запятую к существующему
            */
            $this->headers[$name] .= ','.$value;
        }else{
            $this->headers[$name] = $value;
        }
    }

    /**
    * Установить код ответа
    *
    * @param int $code
    */
    public function setCode($code){
        $this->code = $code;
    }

    /**
    * Выдача ответа клиенту
    *
    */
    public function send(){
        foreach($this->headers as $name => $value) {
            $headerString = $name;
            if( ! empty($value)){
                $headerString .= ': '.$value;
            }
            header($headerString);
        }
        http_response_code($this->code);
        echo $this->body;

    }

    /**
    * Быстрый редирект
    *
    * @param string $URL
    */
    public function redirect($URL){
        $this->setHeader('Location', $URL);
        $this->send();
    }


}

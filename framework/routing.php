<?php

/**
* Объект маршрутизации
*/

namespace Framework;

use Framework\Response as Response;

class Routing{

    /** @var \Framework\request */
    private $request;

    /**
    * Список доступных маршрутов
    */
    private $routes = array(
            '#([-_a-z0-9]+)/([-_a-z0-9]+)/(.*)#'                     => '$1/$2/$3',
            '#([-_a-z0-9]+)/([-_a-z0-9]+)[\?]?(.*)#'                 => '$1/$2/$3',
            '#([-_a-z0-9]+)/([-_a-z0-9]+)/([-_a-z0-9]+)[\?]?(.*)#'   => '$1/$2/$3/$4',
            '#([-_a-z0-9]+)[\?]?(.*)#'                               => '$1/$2',
        );



    public function __construct(\Framework\request $request){
        $this->request = $request;
    }

    /**
    * Получить переданный URI
    * @return string
    */
    protected function getURI(){
        return $this->request->server('REQUEST_URI');
    }

    /**
    * Главный метод выбора контроллера и метода для выполнения действия
    * Осуществляет соответсткие URI маршруту и разбор его на параметры
    * с последующим вызовом соотвествующего метода.
    *
    */
    public function run(){
        try{
            $uri = $this->getURI();
            $uri = ltrim($uri, '/');
            $args = [];
            if(empty($uri)){
                $controllerName = \App\App::$config->get('defaultController');
                $actionName     = \App\App::$config->get('defaultAction');
              }else{
                foreach ($this->routes as $route => $destin){
                     if(preg_match($route, $uri)){
                        $internalRoute = preg_replace($route, $destin, $uri);
                        $segments = explode('/', $internalRoute);
                        $controllerName = ucfirst(array_shift($segments));
                        $actionName = array_shift($segments);
                        parse_str(array_shift($segments), $args);
                        break;
                    }
                }
            }
            $controller = '\Controllers\Controller_'.$controllerName;
            $method = 'Action'.$actionName;

            if( ! class_exists($controller)){
                throw new \Framework\Exception(\Framework\Exception::CONTROLLER_CLASS_NOT_FOUND);
            }

            $obj = new $controller;

            if( ! method_exists($obj, $method)){
                throw new \Framework\Exception(\Framework\Exception::CONTROLLER_ACTION_NOT_FOUND);
            }

            return  $obj->$method($args);;

        }catch(\Exception $e){
            $this->controller_error($e->getMessage());
        }
        return;
    }

    /**
    * Вывод ошибки 404
    *
    */
    protected function _default_error(){
        $response = new Response(html::renderLayout('error_not_found'));
        $response->setCode($response::CODE_NOT_FOUND);
        $response->setHeader("HTTP/1.0 404 Not Found");
        $response->send();
    }

    /**
    * Вывод из-за которой невозможно вызвать метод действия
    *
    * @param string $message
    */
    protected function controller_error($message){
        $response = new Response(html::renderLayout('error_controller', ['message' => $message]));
        $response->send();
    }

}


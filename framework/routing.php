<?php

/**
* Объект маршрутизации
*/

namespace Framework;

use Framework\Response as Response;

class Routing{

    protected $routeFilePath = '';

    /** @var \Framework\request */
    protected $request;

    /**
    * Список доступных маршрутов
    */
     protected $routes = [];

     /**
     * переменные которые подменяются в строке маршрута на регулярные выражения
     */
    protected $routeVariables = array(
        'controller' => '([a-zA-Z-_0-9]+)',
        'action'     => '([a-zA-Z-_0-9]+)',
        'params'     => '{0,1}(\?.*)',
    );



    /**
    * put your comment there...
    *
    * @param \Framework\request $request
    * @param string $path путь к файлу маршрутов
    */
    public function __construct(\Framework\request $request, $path){
        $this->routeFilePath = $path;
        $this->request = $request;
    }

    /**
    * Возвращает список маршрутов
    *
    * @return array
    */
    protected function getRoutes(){
        if(empty($this->routes)){
            $this->routes = $this->loadRoutes();
        }
        return $this->routes;
    }

    /**
    * Получает список маршрутов из файла и
    * проводит необходимые преобразования для использования в preg_match
    *
    * @return array
    */
    protected function loadRoutes(){
        $file = file_get_contents($this->routeFilePath.'/routes.json');
        $routes = json_decode($file, true);
        $routesReg = $this->routesTranslation($routes);
        return $routesReg;
    }

    /**
    * Замена переменных в строке маршрутов на регулярные выражения
    *
    * @param array $routes
    * @return array
    */
    protected function routesTranslation($routes){
        foreach ($routes as $key => $route) {
            $w = explode($route['delimiter'], $route['patern']);
            foreach ($w as $k => $word){
                if(substr($word, 0, 1) == '#'){
                    $w[$k] = $this->routeVariables[substr($word, 1)];
                }
            }
            $route = implode($route['delimiter'], $w);
            $routes[$key]['patern'] = $route;
        }
        return $routes;
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
        $this->getRoutes();
        try{
            $uri = $this->getURI();
            $uri = ltrim($uri, '/');
            $args = [];
            if(empty($uri)){
                $controllerName = \App\App::$config->app_defaultcontroller();
                $actionName     = \App\App::$config->app_defaultaction();
              }else{
                foreach ($this->routes as $routeTitle => $route){
                    $string = '#'.$route['patern'].'#';
                     if(preg_match($string, $uri, $matches)){
                        $controllerName = ucfirst($matches[1]);
                        $actionName = $matches[2];
                        if(!empty($matches[3])){
                            if(substr($matches[3], 0, 1) == "?"){
                                $matches[3] = substr($matches[3], 1);
                            }
                            parse_str($matches[3], $args);
                        }
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


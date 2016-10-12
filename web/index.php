<?php
define('__ROOT__', dirname(__FILE__).'/..');
define('__WEBROOT__', dirname(__FILE__));

require_once(__ROOT__.'/framework/loader.php');

$loader = new \Framework\Loader();

$registry = new \Framework\Registry();
$request = new \Framework\request();
$registry->set('rootPath', __ROOT__);
$registry->set('webRootPath', __WEBROOT__);
$registry->set('configPath', __ROOT__.'/app');
$registry->set('routesPath', __ROOT__.'/app');

\App\App::$config = new App\config($registry->get('configPath'));
\App\App::$registry = $registry;
\App\App::$message = new Framework\message();

$router = new \Framework\Routing($request, $registry->get('routesPath'));

$router->run();

$a = 1;




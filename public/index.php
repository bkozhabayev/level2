<?php

if( !session_id() ) @session_start();
require '../vendor/autoload.php';
use Delight\Auth\Auth;
use DI\ContainerBuilder;
use League\Plates\Engine;
use Aura\SqlQuery\QueryFactory;
use Valitron\Validator;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Engine::class => function() {
        return new Engine('../App/Views');
    },
    PDO::class => function() {
        $driver = 'mysql';
        $host = 'localhost';
        $dbname = 'level3';
        $username = 'root';
        $psw = '';
        return new PDO(
                "$driver:host=$host; dbname=$dbname","$username","$psw");
    },
    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },
    Auth::class => function($container)
    {
        return new Auth($container->get('PDO'), null,null,false);
    },
    Validator::class => function() {
        return new Validator($_POST);
    }
]);
$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
    $r->addRoute('GET', '/test', ['App\Controllers\HomeController', 'test']);
    $r->addRoute('GET', '/check', ['App\Controllers\AuthController', 'check']);
    $r->addRoute('GET', '/check2', ['App\Controllers\AuthController', 'check2']);
    $r->addRoute('POST', '/store', ['App\Controllers\HomeController', 'store']);
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('POST', '/login-handler', ['App\Controllers\AuthController', 'loginHandler']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('POST', '/register-handler', ['App\Controllers\AuthController', 'registerHandler']);
    $r->addRoute('GET', '/verify_email', ['App\Controllers\AuthController', 'emailVerification']);
    $r->addRoute('GET', '/profile', ['App\Controllers\HomeController', 'profile']);
    $r->addRoute('POST', '/profile/edit', ['App\Controllers\HomeController', 'profileEdit']);
    $r->addRoute('POST', '/profile/password', ['App\Controllers\HomeController', 'profilePasswordEdit']);
    $r->addRoute('GET', '/admin', ['App\Controllers\AdminController', 'admin']);
    $r->addRoute('GET', '/admin/allowComment', ['App\Controllers\AdminController', 'allowComment']);
    $r->addRoute('GET', '/admin/disallowComment', ['App\Controllers\AdminController', 'disallowComment']);
    $r->addRoute('GET', '/admin/deleteComment', ['App\Controllers\AdminController', 'deleteComment']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($handler,$vars);
        break;
}

?>








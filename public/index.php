<?php 

declare (strict_types = 1);
error_reporting(E_ALL);

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$container = require 'config/container.php';

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$strategy = (new App\Core\ApplicationStrategy)->setContainer($container);
$router = (new League\Route\Router)->setStrategy($strategy);

$app = $container->get('App\Application');
$app->router($router);
(require 'config/routes.php')($app, $container);
(require 'config/middlewares.php')($app, $container);
$app->process($request);
$app->run();
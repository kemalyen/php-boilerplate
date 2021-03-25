<?php 
return function (App\Application $app, DI\Container $container): void {
    $app->router->map('GET', '/', 'App\Controllers\IndexController::index');


    $app->router->map('GET', '/secret', 'App\Controllers\IndexController::secret')
        ->middleware(new \App\Middlewares\Auth($container));

    $app->router->map('GET', '/user', 'App\Controllers\UserController::user')
        ->middleware(new \App\Middlewares\Auth($container));

    $app->router->map('POST', '/update', 'App\Controllers\UserController::update')
        ->middleware(new \App\Middlewares\Auth($container));

    $app->router->map('POST', '/auth/signup', 'App\Controllers\AuthController::signup');

    $app->router->map('POST', '/auth/signin', 'App\Controllers\AuthController::signin');

};
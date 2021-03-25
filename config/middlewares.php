<?php

return function (App\Application $app, DI\Container $container): void {
    $app->router->middlewares(
        [
            $container->get('Mezzio\Helper\BodyParams\BodyParamsMiddleware'),
        ]);
};
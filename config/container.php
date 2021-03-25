<?php
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

$containerBuilder = new DI\ContainerBuilder;

$containerBuilder->useAnnotations(true);
$containerBuilder->addDefinitions([
    'App\Application' => function (ContainerInterface $c) {
        return new App\Application();
    },
    Doctrine\ORM\EntityManager::class => DI\factory([EntityManager::class, 'create'])
        ->parameter('connection', DI\get('db.params'))
        ->parameter('config', DI\get('doctrine.config')),

    'db.params' => [
        'driver'   => 'pdo_mysql',
        'user'     => 'root',
        'password' => 'root',
        'dbname'   => 'default',
        'host' => 'localhost',
    ],

    'doctrine.config' =>
        Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src/Entities"), true, null, null, false),

    'secret_key' => 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=',
    'app_url' => 'http://example.org',

    Psr\Log\LoggerInterface::class => DI\factory(function () {
        $logger = new Logger('intellect');
        $fileHandler = new StreamHandler('var/app.log', Logger::DEBUG);
        $fileHandler->setFormatter(new LineFormatter());
        $logger->pushHandler($fileHandler);

        return $logger;
    }),

]);
return $containerBuilder->build();
<?php
declare(strict_types=1);

use App\ValueObject\AddressFactory;
use App\ValueObject\PersonFactory;
use Ilex\Slim\RouteStrategies\RouteArgsResolver;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

return [

    AddressFactory::class => DI\autowire(),
    PersonFactory::class => DI\autowire(),

    RequestResponseArgs::class => static function (
        ContainerInterface $c
    ) {
        $routeResolver = new RouteArgsResolver();
        $routeResolver
            ->add($c->get(AddressFactory::class))
            ->add($c->get(PersonFactory::class));


        return new RequestResponseArgs($routeResolver);
    },

    LoggerInterface::class => static function (ContainerInterface $c) {
        $setting = $c->get('logger.settings');
        $logger = new Logger($setting['name']);
        $handler = new StreamHandler($setting['path'], $setting['level']);
        $logger->pushHandler($handler);
        return $logger;
    },

    CacheInterface::class => static function (ContainerInterface $c) {
        return new FilesystemCache('app', 30, __DIR__ . '/../var/cache');
    },
];
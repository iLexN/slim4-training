<?php
declare(strict_types=1);

use App\EventListener\ControllerEventListener;
use App\EventListener\ControllerEventListener1;
use App\ValueObject\AddressFactory;
use App\ValueObject\PersonFactory;
use Ilex\Slim\RouteStrategies\RouteArgsResolver;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Yii\EventDispatcher\Dispatcher;
use Yii\EventDispatcher\Provider\Provider;

return [

    /*
    |--------------------------------------------------------------------------
    | Slim route arg resolver
    |--------------------------------------------------------------------------
    */
    AddressFactory::class => DI\autowire(),
    PersonFactory::class => DI\autowire(),

    RouteArgsResolver::class => DI\autowire()
        ->method('add', DI\get(AddressFactory::class))
        ->method('add', DI\get(PersonFactory::class)),
    RequestResponseArgs::class => DI\autowire(),


    /*
    |--------------------------------------------------------------------------
    | PSR 14
    |--------------------------------------------------------------------------
    */
    ListenerProviderInterface::class => DI\autowire(Provider::class)
        ->method('attach', DI\get(ControllerEventListener1::class))
        ->method('attach', DI\get(ControllerEventListener::class)),

    EventDispatcherInterface::class => DI\autowire(Dispatcher::class),


    /*
    |--------------------------------------------------------------------------
    | PSR 3
    |--------------------------------------------------------------------------
    */
    LoggerInterface::class => static function (ContainerInterface $c) {
        $setting = $c->get('logger.settings');
        $logger = new Logger($setting['name']);
        $handler = new StreamHandler($setting['path'], $setting['level']);
        $logger->pushHandler($handler);
        return $logger;
    },


    /*
    |--------------------------------------------------------------------------
    | PSR 6
    |--------------------------------------------------------------------------
    */
    CacheInterface::class => static function (ContainerInterface $c) {
        $setting = $c->get('cache.file.settings');
        return new FilesystemCache(
            (string) $setting['name'],
            $setting['lifetime'],
            $setting['path']
        );
    },
];
<?php
declare(strict_types=1);

use App\Commands\ArticleCommand\ArticleSaveCommand;
use App\Commands\ArticleCommand\ArticleSaveHandler;
use App\EventListener\Article\PreSave\ArticleDescriptionToSummaryListener;
use App\EventListener\Article\PreSave\ArticleGenerateUrlListener;
use App\EventListener\ControllerEventListener;
use App\EventListener\ControllerEventListener1;
use App\ValueObject\AddressFactory;
use App\ValueObject\PersonFactory;

use Ilex\Slim\RouteStrategies\RouteArgsResolver;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use League\Tactician\CommandBus;
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
    | command bus
    |--------------------------------------------------------------------------
    */

    CommandBus::class => static function (ContainerInterface $c) {
        $commandBus = \League\Tactician\Setup\QuickStart::create(
            [
                ArticleSaveCommand::class => $c->get(ArticleSaveHandler::class),
            ]
        );
        return $commandBus;
    },

    /*
    |--------------------------------------------------------------------------
    | route controller (optional , for DI enableCompilation)
    |--------------------------------------------------------------------------
    */
    \App\Controller\Name::class => DI\autowire(),


    /*
    |--------------------------------------------------------------------------
    | Slim route arg resolver
    |--------------------------------------------------------------------------
    */
    RouteArgsResolver::class => DI\autowire()
        ->method('add', DI\get(AddressFactory::class))
        ->method('add', DI\get(PersonFactory::class))
    ,
    RequestResponseArgs::class => DI\autowire(),


    /*
    |--------------------------------------------------------------------------
    | PSR 14
    |--------------------------------------------------------------------------
    */
    //\App\EventListener\Article\PreSave\ArticleGenerateUrlListener::class => \DI\autowire(),

    ListenerProviderInterface::class =>
        DI\autowire(Provider::class)
        ->method('attach', DI\autowire(ControllerEventListener1::class))
        ->method('attach', DI\autowire(ControllerEventListener::class))
        ->method('attach', DI\autowire(ArticleDescriptionToSummaryListener::class))
        ->method('attach', DI\autowire(ArticleGenerateUrlListener::class))
    ,

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
    | PSR 16
    |--------------------------------------------------------------------------
    */
    CacheInterface::class => static function (ContainerInterface $c) {
        $setting = $c->get('cache.file.settings');
        return new FilesystemCache(
            (string)$setting['name'],
            $setting['lifetime'],
            $setting['path']
        );
    },
];
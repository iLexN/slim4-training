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
use Http\Factory\Discovery\HttpFactory;
use Ilex\Slim\RouteStrategies\RouteArgsResolver;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use League\Tactician\CommandBus;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Contracts\Cache\CacheInterface as SymfonyCache;
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;

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
    | PSR 17
    |--------------------------------------------------------------------------
    */
    RequestFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'requestFactory',
    ]),
    ResponseFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'responseFactory',
    ]),
    ServerRequestFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'serverRequestFactory',
    ]),
    StreamFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'streamFactory',
    ]),
    UriFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'uriFactory',
    ]),
    UploadedFileFactoryInterface::class => DI\factory([
        HttpFactory::class,
        'uploadedFileFactory',
    ]),

    'currentServerRequest' => static function () {
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        return $serverRequestCreator->createServerRequestFromGlobals();
    },


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
    | PSR 6
    |--------------------------------------------------------------------------
    */
    'psr6File' => static function (ContainerInterface $c) {
        $setting = $c->get('cache.file.settings');
        return new FilesystemAdapter(
            $setting['namespace'],
            $setting['lifetime'],
            $setting['path']
        );
    },
    'psr6Redis' => static function (ContainerInterface $c) {
        $setting = $c->get('cache.redis.settings');
        $client = RedisAdapter::createConnection(
            $setting['dns']
        );
        return new RedisAdapter(
            $client,
            $setting['namespace'],
            $setting['lifetime']
        );
    },

    CacheItemPoolInterface::class => static function (ContainerInterface $c) {
        return $c->get('psr6Redis');
    },


    /*
    |--------------------------------------------------------------------------
    | PSR 16
    |--------------------------------------------------------------------------
    */
    CacheInterface::class => static function (ContainerInterface $c) {
        return new Psr16Cache(
            $c->get(CacheItemPoolInterface::class)
        );
    },


    /*
    |--------------------------------------------------------------------------
    | Cache with cache tag feature. Not a psr interface
    |--------------------------------------------------------------------------
    */
    SymfonyCache::class => DI\autowire(TagAwareAdapter::class)
        ->constructor(DI\get('psr6File')),
];
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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
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
use Symfony\Contracts\Cache\ItemInterface;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;

return [

    /*
    |--------------------------------------------------------------------------
    | command bus
    |--------------------------------------------------------------------------
    */
    CommandBus::class => static function (ContainerInterface $container) {
        return \League\Tactician\Setup\QuickStart::create(
            [
                ArticleSaveCommand::class => $container->get(ArticleSaveHandler::class),
            ]
        );
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
    | PSR 17 auto discover
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
            ->method('attach',
                DI\autowire(ArticleDescriptionToSummaryListener::class))
            ->method('attach', DI\autowire(ArticleGenerateUrlListener::class))
    ,

    EventDispatcherInterface::class => DI\autowire(Dispatcher::class),


    /*
    |--------------------------------------------------------------------------
    | PSR 3
    |--------------------------------------------------------------------------
    */
    LoggerInterface::class => static function (ContainerInterface $container) {
        $setting = $container->get('logger.settings');
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
    'psr6File' => static function (ContainerInterface $container) {
        $setting = $container->get('cache.file.settings');
        return new FilesystemAdapter(
            $setting['namespace'],
            $setting['lifetime'],
            $setting['path']
        );
    },
    'psr6Redis' => static function (ContainerInterface $container) {
        $setting = $container->get('cache.redis.settings');
        $client = RedisAdapter::createConnection(
            $setting['dns']
        );
        return new RedisAdapter(
            $client,
            $setting['namespace'],
            $setting['lifetime']
        );
    },

    CacheItemPoolInterface::class => static function (
        ContainerInterface $container
    ) {
        return $container->get('psr6File');
    },


    /*
    |--------------------------------------------------------------------------
    | PSR 16
    |--------------------------------------------------------------------------
    */
    CacheInterface::class => static function (ContainerInterface $container) {
        return new Psr16Cache(
            $container->get(CacheItemPoolInterface::class)
        );
    },


    /*
    |--------------------------------------------------------------------------
    | Cache with cache tag feature. Not a psr interface
    |--------------------------------------------------------------------------
    */
    SymfonyCache::class => DI\autowire(TagAwareAdapter::class)
        ->constructor(DI\get('psr6File')),

    /*
    |--------------------------------------------------------------------------
    | doctrine
    |--------------------------------------------------------------------------
    */
    EntityManagerInterface::class => static function () {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/../src'],
            $isDevMode);
        $conn = \Doctrine\DBAL\DriverManager::getConnection([
            'dbname' => 'ilex1',
            'user' => 'ggg',
            'password' => 'ggg',
            'host' => '127.0.0.1',
            //'port' => '3367',
            'driver' => 'pdo_mysql',
        ], new \Doctrine\DBAL\Configuration());
        return EntityManager::create($conn, $config);
    },

    \App\Doctrine\UserRepository::class => static function (
        EntityManagerInterface $entityManager
    ) {
        return $entityManager->getRepository(\App\Doctrine\User::class);
    },

    SchemaFactory::class => static function (
        ContainerInterface $container,
        CacheInterface $cache
    ) {
        $factory = new SchemaFactory($cache, $container);
        $factory->addControllerNamespace('Domain\\GraphQlQuery')
            ->addTypeNamespace('Domain\\');
        return $factory;
    },
//    Schema::class => static function (
//        ContainerInterface $container,
//        CacheInterface $cache,
//        SymfonyCache $sfcache
//    ) {
//
//        $schema = $sfcache->get('graphql_schema',
//            function (ItemInterface $item) use ($container, $cache) {
//                dump('no schema cache');
//                //$item->expiresAfter(3600);
//                $factory = new SchemaFactory($cache, $container);
//                $factory->addControllerNamespace('Domain\\GraphQlQuery')
//                    ->addTypeNamespace('Domain\\');
//
//                $schema = $factory->createSchema();
//                return $schema;
//            });
//
//        $schema = $cache->get('g2');
//        if ($schema === null) {
//            dump('no schema cache for 16');
//            $factory = new SchemaFactory($cache, $container);
//            $factory->addControllerNamespace('Domain\\GraphQlQuery')
//                ->addTypeNamespace('Domain\\');
//
//            $schema = $factory->createSchema();
//            $cache->set('g2', $schema, 10000);
//        }
//        return $schema;
//    },

];

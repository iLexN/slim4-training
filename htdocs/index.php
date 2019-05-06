<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\Address;
use App\Controller\Name;
use DI\ContainerBuilder;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

//VarDumper::setHandler(static function ($var) {
//    $clone = new VarCloner();
//    $dumper = new ServerDumper('tcp://127.0.0.1:9912',new CliDumper(),[
//        'cli' => new CliContextProvider(),
//        'source' => new SourceContextProvider(),
//    ]);
//    $dumper->dump($clone->cloneVar($var));
//});

// Instantiate PHP-DI Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    __DIR__ . '/../app/settings.php',
    __DIR__ . '/../app/dependencies.php'
);
//$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
/** @var ContainerInterface $container */
$container = $containerBuilder->build();


// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();

$urlResolver = $container->get(RequestResponseArgs::class);


$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy($urlResolver);

$app->get('/hello/{uid:[0-9]+}', Name::class);
$app->get('/hello/{name}', Name::class);

$app->get('/add/{aid:[0-9]+}', Address::class);
$app->get('/add/{address}', Address::class);

$app->get('/', function ($request, $response) {
    $response->getBody()->write("Home");
    return $response;
});

/**
 * The constructor of ErrorMiddleware takes in 5 parameters
 * @param CallableResolverInterface $callableResolver -> CallableResolver implementation of your choice
 * @param ResponseFactoryInterface $responseFactory -> ResponseFactory implementation of your choice
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, true, true);
$app->add($errorMiddleware);

//$routeResolver = $app->getRouteResolver();
//$routingMiddleware = new RoutingMiddleware($routeResolver);
//$app->add($routingMiddleware);

/**
 * The constructor of ErrorMiddleware takes in 5 parameters
 * @param CallableResolverInterface $callableResolver -> CallableResolver implementation of your choice
 * @param ResponseFactoryInterface $responseFactory -> ResponseFactory implementation of your choice
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();
$errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, true, true);
$app->add($errorMiddleware);

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$app->run($request);
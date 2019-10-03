<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\Address;
use App\Controller\GraphQlController;
use App\Controller\Name;
use DI\ContainerBuilder;
use Ilex\Slim\RouteStrategies\Strategies\RequestResponseArgs;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;

VarDumper::setHandler(static function ($var) {
    $clone = new VarCloner();
    $dumper = new ServerDumper('tcp://127.0.0.1:9912', new CliDumper(), [
        'cli' => new CliContextProvider(),
        'source' => new SourceContextProvider(),
    ]);
    $dumper->dump($clone->cloneVar($var));
});

include __DIR__ . '/../app/db.php';

// Instantiate PHP-DI Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    __DIR__ . '/../app/settings.php',
    __DIR__ . '/../app/dependencies.php'
);
//$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
$container = $containerBuilder->build();


/*
|--------------------------------------------------------------------------
| Instantiate the app
|--------------------------------------------------------------------------
*/
AppFactory::setContainer($container);
AppFactory::setResponseFactory($container->get(ResponseFactoryInterface::class));

$app = AppFactory::create();


/*
|--------------------------------------------------------------------------
| url resolver
|--------------------------------------------------------------------------
*/
$urlResolver = $container->get(RequestResponseArgs::class);
$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy($urlResolver);
//$routeCollector->setCacheFile(__DIR__.'/../var/cache/route.cache');

/*
|--------------------------------------------------------------------------
| route
|--------------------------------------------------------------------------
*/
$app->post('/graphql', GraphQlController::class);
$app->get('/hello/{uid:[0-9]+}', Name::class);
$app->get('/hello/{name}', Name::class);

$app->get('/add/{aid:[0-9]+}', Address::class);
$app->get('/add/{address}', Address::class);

$app->get('/news[/{params:.*}]', function ($request, $response, $args) {
    // $params is an array of all the optional segments

    $params = explode('/', $args);
    dump($params);
    $response->getBody()->write("params");
    return $response;
});

$app->get('/', function ($request, $response) {
    $response->getBody()->write("Home");


    return $response;
});


//$routeResolver = $app->getRouteResolver();
//$routingMiddleware = new RoutingMiddleware($routeResolver);
//$app->add($routingMiddleware);

/**
 * The constructor of ErrorMiddleware takes in 5 parameters
 *
 * @param CallableResolverInterface $callableResolver -> CallableResolver
 *     implementation of your choice
 * @param ResponseFactoryInterface $responseFactory -> ResponseFactory
 *     implementation of your choice
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 * Note: This Middleware should be added last. It will not handle any
 *     exceptions/errors for Middleware added after it.
 */
//$callableResolver = $app->getCallableResolver();
//$responseFactory = $app->getResponseFactory();
//$errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, true, true);
//$app->add($errorMiddleware);

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/
$app->add(new \App\Middleware\StopWatchMiddleware());

$request = $container->get('currentServerRequest');

$app->run($request);

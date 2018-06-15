<?php

// define namespace
namespace Example;


// define aliases
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Http\{HttpRequest, HttpResponse};
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

// autoload 3rd party packages
require __DIR__ . '/../vendor/autoload.php';


// load environment variables
$dotenv = new Dotenv(__DIR__, '../.env');
$dotenv->load();


// setup error handler
$whoops = new Run();
if (getenv('APP_ENVIRONMENT') == 'dev')
{
    $whoops->pushHandler(new PrettyPageHandler());
}
    else
{
    $whoops->pushHandler(function ($e)
    {
        echo 'Friendly error page and send an email to the developer';
    });
}
$whoops->register();

// setup dependency injection
$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/dependencies.php');
$container = $containerBuilder->build();

$request = new HttpRequest($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER, file_get_contents('php://input'));
$response = new HttpResponse();

// initiate twig new object here?
// no, we have allready initiated twig in via php-di in dependencies.php, we will call it at the end of each class

// setup routing
$routeDefinitionCallback = function (\FastRoute\RouteCollector $routeCollector)
{
    $routes = require __DIR__ . '/../bootstrap/routes.php';
    foreach ($routes as $route)
    {
        $routeCollector->addRoute($route[0], $route[1], $route[2]);
    }
};
$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1];
        $parameters = $routeInfo[2];

        // We could do $container->get($controller) but $container->call() does that automatically
        $container->call($controller, $parameters);
        break;
}

// this part might become decaprated
// setup templating
foreach ($response->getHeaders() as $header) {
    header($header, false);
}
echo $response->getContent();

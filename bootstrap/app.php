<?php

// define namespace
namespace Example;


// define aliases
use Dotenv\Dotenv;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;


// autoload 3rd party packages
require __DIR__ . '/../vendor/autoload.php';


// load environment variables
$dotenv = new Dotenv(__DIR__, '../.env');
$dotenv->load();


// setup error handler
$whoops = new Run;
if (getenv('APP_ENVIRONMENT') == 'dev')
{
    $whoops->pushHandler(new PrettyPageHandler);
}
    else
{
    $whoops->pushHandler(function($e){
        echo 'Friendly error page and send an email to the developer';
    });
}
$whoops->register();


// setup dependencies



// setup routing



// setup templating


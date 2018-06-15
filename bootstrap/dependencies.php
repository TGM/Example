<?php

use function DI\create;

return [

    // Configure Twig
    Twig_Environment::class => function () {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views/pages');
        return new Twig_Environment($loader);
    },

];

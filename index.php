<?php

use Nette\Diagnostics\Debugger,
    Nette\Application\Routers\Route;


// Load libraries
require __DIR__ . '/libs/Nette/Nette/loader.php';


// Enable Nette Debugger for error visualisation & logging
Debugger::$logDirectory = __DIR__ . '/log';
Debugger::enable();


// Configure application
$configurator = new Nette\Config\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/app/config.neon');
$container = $configurator->createContainer();


// Setup router
// http://davidgrudl.com/[cs|en]
$container->router[] = new Route('[<lang (?-i)defaul|en>]', function($presenter, $lang) use ($container) {
    if (!$lang) {
        $lang = $container->httpRequest->detectLanguage(array('en', 'cs')) ?: 'cs';
        return $presenter->redirectUrl($lang);
    }

    return $presenter->createTemplate()
        ->setFile(__DIR__ . '/app/' . $lang . '.latte');
});

// http://davidgrudl.com/sources
$container->router[] = new Route('default', function($presenter) {
    return $presenter->createTemplate()
        ->setFile(__DIR__ . '/app/default.latte');
});


// Run the application!
$container->application->run();
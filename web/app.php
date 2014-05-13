<?php

use com\publicuhc\ts3auth\ControllerResolver;
use com\publicuhc\ts3auth\ParameterBagNested;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$loader = require __DIR__ . '/../vendor/autoload.php';

// look inside the src directory
$locator = new FileLocator(array(__DIR__ . '/../config/'));
$loader = new Symfony\Component\Routing\Loader\YamlFileLoader($locator);
$collection = $loader->load('routes.yml');

$matcher = new UrlMatcher($collection, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher));

$container = new ContainerBuilder(new ParameterBagNested());
$diLoader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
$diLoader->load('config.yml');

$resolver = new ControllerResolver($container);
$kernel = new HttpKernel($dispatcher, $resolver);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
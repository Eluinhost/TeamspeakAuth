<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$loader = require __DIR__ . '/../vendor/autoload.php';

// look inside the src directory
$locator = new FileLocator(array(__DIR__ . '/../src/'));
$loader = new YamlFileLoader($locator);
$collection = $loader->load('routes.yml');

$container = new ContainerBuilder();
$diLoader = new FileLocator(__DIR__ . '/../src/');
$diLoader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $diLoader);
$diLoader->load('services.yml');

$matcher = new UrlMatcher($collection, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher));

$resolver = new ControllerResolver();
$kernel = new HttpKernel($dispatcher, $resolver);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
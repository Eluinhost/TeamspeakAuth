<?php

use PublicUHC\TeamspeakAuth\ControllerResolver;
use PublicUHC\TeamspeakAuth\ParameterBagNested;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\TemplateNameParser;

$loader = require __DIR__ . '/../vendor/autoload.php';

// look inside the src directory
$locator = new FileLocator(array(__DIR__ . '/../config/'));
$loader = new Symfony\Component\Routing\Loader\YamlFileLoader($locator);
$collection = $loader->load('routes.yml');

$matcher = new UrlMatcher($collection, new RequestContext());

$dispatcher = new EventDispatcher();
$routerListener = new RouterListener($matcher);
$dispatcher->addSubscriber($routerListener);

$container = new ContainerBuilder(new ParameterBagNested());
$diLoader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
$diLoader->load('config.yml');

$request = Request::createFromGlobals();

$requestContext = new RequestContext();
$requestContext->fromRequest($request);

$urlGenerator = new UrlGenerator($collection, $requestContext);

$twigLoader = new Twig_Loader_Filesystem(__DIR__ . './../templates/');
$twigEnvironment = new Twig_Environment($twigLoader);
$twigEnvironment->addExtension(new RoutingExtension($urlGenerator));

$templating = new TwigEngine($twigEnvironment, new TemplateNameParser());
$container->set('templating', $templating);

$resolver = new ControllerResolver($container);
$kernel = new HttpKernel($dispatcher, $resolver);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
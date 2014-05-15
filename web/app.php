<?php

use Assetic\Asset\AssetCollection;
use Assetic\Asset\GlobAsset;
use Assetic\AssetWriter;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Resource\DirectoryResource;
use Assetic\Filter\CssRewriteFilter;
use Assetic\FilterManager;
use PublicUHC\TeamspeakAuth\AssetExtension;
use PublicUHC\TeamspeakAuth\AsseticLoader;
use PublicUHC\TeamspeakAuth\ControllerResolver;
use PublicUHC\TeamspeakAuth\ParameterBagNested;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Templating\Asset\PathPackage;
use Symfony\Component\Templating\Helper\AssetsHelper;
use Symfony\Component\Templating\TemplateNameParser;

$loader = require __DIR__ . '/../vendor/autoload.php';

/**
 * Create the request and it's context
 */
$request = Request::createFromGlobals();

$requestContext = new RequestContext();
$requestContext->fromRequest($request);

/**
 * Set up the file locator to get our config files from
 */
$configLocator = new FileLocator(array(__DIR__ . '/../config/'));

/*
 * Load the routing from the file /config/routes.yml
 */
$loader = new Symfony\Component\Routing\Loader\YamlFileLoader($configLocator);
$collection = $loader->load('routes.yml');

/*
 * Load the DI container from the file /config/config.yml
 */
$container = new ContainerBuilder(new ParameterBagNested());
$diLoader = new Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $configLocator);
$diLoader->load('config.yml');

/**
 * Set up twig and add it to the container
 */
$twigEnvironment = new Twig_Environment(new Twig_Loader_Filesystem(__DIR__ . './../templates/'));
$twigEnvironment->addExtension(new RoutingExtension(new UrlGenerator($collection, $requestContext)));
$templating = new TwigEngine($twigEnvironment, new TemplateNameParser());
$container->set('templating', $templating);

/**
 * Set up asset extension
 */
$assetsHelper = new AssetsHelper($request->getBasePath());

$assetsHelper->addPackage('bootstrap', new PathPackage('assets/bootstrap/dist'));
$assetsHelper->addPackage('jquery', new PathPackage('assets/jquery/dist'));

$assetsExtension = new AssetExtension($assetsHelper);
$twigEnvironment->addExtension($assetsExtension);

/**
 * Set up the kernel
 */
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener(new UrlMatcher($collection, $requestContext)));
$kernel = new HttpKernel($dispatcher, new ControllerResolver($container));

/**
 * Handle the request
 */
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
<?php

use PublicUHC\TeamspeakAuth\Container\ProjectContainer;
use PublicUHC\TeamspeakAuth\ParameterBagNested;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();
$isDebug = false;
$projectRoot = dirname(dirname(__FILE__));

$containerFile = $projectRoot . '/cache/container/ProjectContainer.php';
$containerConfigCache = new ConfigCache($containerFile, $isDebug);

if (!$containerConfigCache->isFresh()) {
    $container = new ContainerBuilder(new ParameterBagNested());
    $container->setParameter('global.root', $projectRoot);

    $configLocator = new FileLocator($projectRoot . '/config/');
    $diLoader = new YamlFileLoader($container, $configLocator);
    $diLoader->load('config.yml');

    $container->compile();

    $dumper = new PhpDumper($container);
    $containerConfigCache->write(
        $dumper->dump([
            'class' => 'ProjectContainer',
            'namespace' => 'PublicUHC\TeamspeakAuth\Container'
        ]),
        $container->getResources()
    );
}

require_once $containerFile;

$container = new ProjectContainer();

$response = $container->get('framework')->handle($request);
$response->send();
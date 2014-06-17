<?php

use PublicUHC\TeamspeakAuth\Container\ProjectContainer;
use PublicUHC\TeamspeakAuth\ParameterBagNested;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once __DIR__ . '/../vendor/autoload.php';

$isDebug = false;
$projectRoot = dirname(dirname(__FILE__));

$containerFile = $projectRoot . '/cache/container/ProjectContainer.php';
$containerConfigCache = new ConfigCache($containerFile, $isDebug);

if (!$containerConfigCache->isFresh()) {
    $container = new ContainerBuilder(new ParameterBagNested());
    $container->setParameter('global.root', $projectRoot);

    $container->setProxyInstantiator(new RuntimeInstantiator());

    $configLocator = new FileLocator($projectRoot . '/config/');
    $diLoader = new YamlFileLoader($container, $configLocator);
    $diLoader->load('config.yml');

    $container->compile();

    $dumper = new PhpDumper($container);
    $dumper->setProxyDumper(new ProxyDumper());
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

$application = $container->get('console_application');
$application->run();
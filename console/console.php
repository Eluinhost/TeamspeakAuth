<?php

use PublicUHC\TeamspeakAuth\Commands\CleanContainerCommand;
use PublicUHC\TeamspeakAuth\Commands\CleanRoutingCommand;
use PublicUHC\TeamspeakAuth\Commands\UpdateConfigCommand;
use PublicUHC\TeamspeakAuth\Container\ProjectContainer;
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

$application = null;

try {
    $containerConfigCache = new ConfigCache($containerFile, $isDebug);

    if (!$containerConfigCache->isFresh()) {
        $container = new ContainerBuilder();
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

    $projectContainer = new ProjectContainer();

    $application = $projectContainer->get('console_application');
} catch (Exception $ex) {
    echo $ex->getMessage()."\n";
    echo "Exception building project container, only configuration command will be available\n";
}

if($application == null) {
    $application = new Application();
}

//load commands without DI needs

$application->add(new UpdateConfigCommand());
$application->add(new CleanContainerCommand($projectRoot));
$application->add(new CleanRoutingCommand($projectRoot));

$application->run();
<?php

namespace PublicUHC\TeamspeakAuth;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerResolver extends \Symfony\Component\HttpKernel\Controller\ControllerResolver {

    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container the container
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->container = $container;
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     *
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode('::', $controller, 2);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $object = new $class();

        if($object instanceof ContainerAwareInterface) {
            $object->setContainer($this->container);
        }

        return array($object, $method);
    }
} 
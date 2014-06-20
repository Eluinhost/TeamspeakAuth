<?php
namespace PublicUHC\TeamspeakAuth\Controllers;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends ContainerAware {

    public function onException(FlattenException $exception) {
        $methodName = 'on' . $exception->getStatusCode() . 'CodeException';
        if(!method_exists($this, $methodName)) {
            $methodName = 'on500CodeException';
        }
        return $this->{$methodName}($exception);
    }

    public function on500CodeException(FlattenException $exception) {
        return new Response($this->container->get('templating')->render('500.html.haml'));
    }

    public function on404CodeException(FlattenException $exception) {
        return new Response($this->container->get('templating')->render('404.html.haml'));
    }

} 
<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PartialsController extends Controller {

    /**
     * Render the app page
     *
     * @Route("/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('PublicUHCTeamspeakAuthBundle:TeamspeakAuth:app.html.haml');
    }

    /**
     * Render a partial for angular
     *
     * @Route("/partials/{name}")
     *
     * @param $name string the name of the partial to render
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException if template not found
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderPartialAction($name)
    {
        try {
            return $this->render("PublicUHCTeamspeakAuthBundle:Partials:$name.html.haml");
        } catch (\InvalidArgumentException $ex) {
            throw $this->createNotFoundException();
        }
    }
} 
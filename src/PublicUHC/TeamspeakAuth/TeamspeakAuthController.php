<?php

namespace PublicUHC\TeamspeakAuth;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamspeakAuthController extends ContainerAware {

    public function authAction() {
        //TODO
    }

    public function requestTeamspeakAction(Request $request) {
        $response = new JsonResponse();

        $tsName = $request->query->get('ts_name');

        if( $tsName == null || strlen($tsName) == 0 || strlen($tsName) > 30 ) {
            $response->setStatusCode(400);
            $response->setData([
                'error' => 'Invalid teamspeak name provided'
            ]);
            return $response;
        }

        /**
         * @var $ts3 TeamspeakHelper
         */
        $ts3 = $this->container->get('ts3interface');

        try {

            $client = $ts3->getClientForName($tsName);

            if (null == $client) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Teamspeak user not found'
                ]);
                return $response;
            }

            $uuid = $ts3->getUUIDForClient($client);

            $codeRepository = $this->container->get('tscodes');
            $codeRepository->insertCodeForUserID($uuid);

            $response->setData([
                'UUID' => $uuid
            ]);
            return $response;

        } catch ( \TeamSpeak3_Exception $ignored ) {
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error contacting the teamspeak server'
            ]);
            return $response;
        } catch ( \PDOException $ex) {
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error connecting to the database'
            ]);
            return $response;
        }
    }

    public function indexAction() {
        $templating = $this->container->get('templating');
        return new Response($templating->render('index.html.twig'));
    }
} 
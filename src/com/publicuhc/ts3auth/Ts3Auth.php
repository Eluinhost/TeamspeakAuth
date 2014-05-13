<?php

namespace com\publicuhc\ts3auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Ts3Auth extends ContainerAwareInterface {

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
        $ts3 = $this->get('ts3interface');

        $client = $ts3->getClientForName($tsName);

        if( null == $client ) {
            $response->setStatusCode(400);
            $response->setData([
                'error' => 'Teamspeak user not found'
            ]);
            return $response;
        }

        $uuid = $ts3->getUUIDForClient($client);
        $randomCode = substr(md5(rand()), 0, 10);

        //TODO insert UUID and code into database

        $response->setData(array(
            'UUID' => $uuid
        ));
        return $response;
    }

    public function indexAction() {
        return new Response('index');
    }
} 
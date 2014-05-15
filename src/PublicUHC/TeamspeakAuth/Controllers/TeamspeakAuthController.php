<?php

namespace PublicUHC\TeamspeakAuth\Controllers;

use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamspeakAuthController extends ContainerAware {

    public function authAction(Request $request) {
        $response = new JsonResponse();

        $ts_uuid = $request->query->get('ts_uuid');
        $ts_code = $request->query->get('ts_code');
        $mc_uuid = $request->query->get('mc_uuid');
        $mc_code = $request->query->get('mc_code');

        try {
            $tsCodes = $this->container->get('tscodes');
            $mcCodes = $this->container->get('mccodes');

            if (!$tsCodes->doesCodeMatchForUserID($ts_code, $ts_uuid)) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid code for the given Teamspeak UUID'
                ]);
                return $response;
            }

            //TODO check the rest of the parameters

            //TODO run the teamspeak auth part

            //TODO delete the codes

            return $response;
        } catch ( \PDOException $ex ) {
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error connecting to the database'
            ]);
            return $response;
        }
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
        $ts3 = $this->container->get('teamspeakhelper');

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
            $code = $codeRepository->insertCodeForUserID($uuid);

            $timeToLast = $this->container->getParameter('minutesToLast');

            $client->message("[Verification Codes] UUID: '{$uuid}' AUTH CODE: '{$code}'. This code work for the next {$timeToLast} minutes");

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
<?php

namespace PublicUHC\TeamspeakAuth\Controllers;

use PublicUHC\TeamspeakAuth\Helpers\MinecraftHelper;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use PublicUHC\TeamspeakAuth\Repositories\CodeRepository;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TeamSpeak3_Node_Client;

class TeamspeakAuthController extends ContainerAware {

    public function authAction(Request $request) {
        $response = new JsonResponse();

        $ts_uuid = $request->query->get('ts_uuid');
        $ts_code = $request->query->get('ts_code');
        $mc_uuid = $request->query->get('mc_uuid');
        $mc_code = $request->query->get('mc_code');

        try {
            /**
             * @var $tsCodes CodeRepository
             * @var $mcCodes CodeRepository
             */
            $tsCodes = $this->container->get('tscodes');
            $mcCodes = $this->container->get('mccodes');

            if (!$tsCodes->doesCodeMatchForUserID($ts_code, $ts_uuid)) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid code for the given Teamspeak UUID'
                ]);
                return $response;
            }

            if (!$mcCodes->doesCodeMatchForUserID($mc_code, $mc_uuid)) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid code for the given Minecraft username'
                ]);
                return $response;
            }

            /** @var $tsHelper TeamspeakHelper */
            $tsHelper = $this->container->get('teamspeakhelper');

            /** @var $client Teamspeak3_Node_Client */
            $client = $tsHelper->getClientByUUID($ts_uuid);

            //set the description
            $tsHelper->setClientDescription($client, $mc_uuid);

            //add the required server group
            $groupID = $this->container->getParameter('teamspeak.group_id');
            $client->remServerGroup($groupID);
            $client->addServerGroup($groupID);

            /** @var $mcHelper MinecraftHelper */
            $mcHelper = $this->container->get('minecrafthelper');

            $playerIcon = $mcHelper->getIconForUsername($mc_uuid);
            $tsHelper->setClientIcon($client, $playerIcon);

            $tsCodes->removeForUserID($ts_uuid);
            $mcCodes->removeForUserID($mc_uuid);

            return $response;
        } catch ( \PDOException $ex ) {
            error_log($ex->getMessage());
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error connecting to the database'
            ]);
            return $response;
        } catch ( \TeamSpeak3_Exception $ex ) {
            error_log($ex->getMessage());
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'There was a problem with Teamspeak, please try again later'
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

    public function teamspeakRequestPageAction() {
        $templating = $this->container->get('templating');
        return new Response($templating->render('teamspeakrequest.html.twig'));
    }

    public function verifyAccountPageAction($uuid) {
        if(null == $uuid) {
            error_log($uuid);
            throw new NotFoundHttpException();
        }

        $templating = $this->container->get('templating');
        return new Response($templating->render('accountverification.html.twig', ['ts_uuid'=>$uuid]));
    }
} 
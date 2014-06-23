<?php
namespace PublicUHC\TeamspeakAuth\Controllers;


use Doctrine\ORM\EntityManager;
use PublicUHC\TeamspeakAuth\Entities\TeamspeakCode;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use TeamSpeak3_Exception;

class TeamspeakCodeController extends ContainerAware {

    public function requestTeamspeakCodeAction($username) {
        if( $username == null || strlen($username) == 0 || strlen($username) > 30 ) {
            throw new BadRequestHttpException('Invalid teamspeak name provided');
        }

        /**
         * @var $ts3 TeamspeakHelper
         */
        $ts3 = $this->container->get('teamspeakhelper');

        try {

            $client = $ts3->getClientForName($username);

            if (null == $client) {
                throw new BadRequestHttpException('Teamspeak user not online');
            }

            $uuid = $ts3->getUUIDForClient($client);

            $account = $ts3->updateLastClientUsername($client);

            $code = new TeamspeakCode();
            $account->getCodes()->clear();
            $account->getCodes()->add($code);
            $code->setAccount($account);

            /** @var $entityManager EntityManager */
            $entityManager = $this->container->get('entityManager');
            $entityManager->persist($code);
            $entityManager->persist($account);
            $entityManager->flush();

            $timeToLast = $this->container->getParameter('minutesToLast');
            $codeString = $code->getCode();
            $client->message("[Verification Code] AUTH CODE: '{$codeString}'. This code work for the next {$timeToLast} minutes");

            return new JsonResponse([
                'UUID' => $uuid
            ]);
        } catch ( TeamSpeak3_Exception $ignored ) {
            throw new BadRequestHttpException('Error contacting the teamspeak server');
        }
    }
} 
<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Doctrine\ORM\EntityManager;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use TeamSpeak3_Exception;

class TeamspeakCodeController extends Controller {

    /**
     * @Route("/api/v1/teamspeakCodes/{username}", defaults={"_format"="json"}, requirements={"username"=".+"})
     * @Method({"GET"})
     *
     * @param $username
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function requestTeamspeakCodeAction($username) {
        if( $username == null || strlen($username) == 0 || strlen($username) > 30 ) {
            throw new BadRequestHttpException('Invalid teamspeak name provided');
        }

        /**
         * @var $ts3 TeamspeakHelper
         */
        $ts3 = $this->get('teamspeak_helper');

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
            $entityManager = $this->get('doctrine.orm.entity_manager');
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
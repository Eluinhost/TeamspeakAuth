<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use TeamSpeak3_Exception;

/**
 * Class TeamspeakCodeController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api")
 */
class TeamspeakCodeController extends FOSRestController {

    /**
     * @ApiDoc(
     * description="Generates a new code for the teamspeak account with the given name",
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount",
     * tags={"website"}
     * )
     * @Put("/v1/teamspeak_codes", name="api_v1_teamspeak_code_request")
     * @RequestParam(name="username", description="Teamspeak username to send a code to")
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

            return $this->view($account);
        } catch ( TeamSpeak3_Exception $ignored ) {
            throw new BadRequestHttpException('Error contacting the teamspeak server');
        }
    }
} 
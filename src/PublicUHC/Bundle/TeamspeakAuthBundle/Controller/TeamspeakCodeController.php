<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use TeamSpeak3_Exception;

/**
 * Class TeamspeakCodeController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api", defaults={"_format"="json"})
 */
class TeamspeakCodeController extends FOSRestController {

    /**
     * @ApiDoc(
     * section="Teamspeak Accounts",
     * description="Generates a new code for the teamspeak account with the given name",
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount",
     * tags={"website"},
     * statusCodes={
     *      200="On success",
     *      400="On invalid parameters",
     *      404="On username not found or not online",
     *      503="On failure to reach Teamspeak server"
     * }
     * )
     * @Route("/v1/teamspeak_codes", name="api_v1_teamspeak_code_request")
     * @Method({"PUT"})
     * @RequestParam(name="username", description="Teamspeak username to send a code to")
     */
    public function apiV1RequestTeamspeakCodeAction($username) {
        if( $username == null || strlen($username) == 0 || strlen($username) > 30 )
            throw new NotFoundHttpException('Invalid teamspeak name provided');

        /** @var $ts3 TeamspeakHelper */
        $ts3 = $this->get('teamspeak_helper');

        try {
            $client = $ts3->getClientForName($username);

            if (null == $client)
                throw new NotFoundHttpException('Teamspeak user not online');

            $account = $ts3->updateLastClientUsername($client);
            $account->getCodes()->clear();

            $code = new TeamspeakCode();
            $code->setAccount($account);

            $account->getCodes()->add($code);

            /** @var $entityManager EntityManager */
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($code);
            $entityManager->persist($account);
            $entityManager->flush();

            $client->message("[Verification Code] AUTH CODE: '{$code->getCode()}'. This code work for the next {$this->container->getParameter('minutesToLast')} minutes");

            return $this->view($account);
        } catch ( TeamSpeak3_Exception $ignored ) {
            throw new ServiceUnavailableHttpException('Error contacting the teamspeak server');
        }
    }
} 
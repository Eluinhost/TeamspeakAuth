<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\AuthenticationRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftCodeRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCodeRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TeamSpeak3_Exception;

/**
 * Class AuthenticationController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api", defaults={"_format"="json"})
 */
class AuthenticationController extends FOSRestController
{

    /**
     * @Route("/v1/authentications", name="api_v1_authentications_new")
     * @Method({"POST"})
     *
     * @ApiDoc(
     * section="Authentication",
     * description="Add a new authentication to the system between a Teamspeak account and a Minecraft account",
     * tags={"website"},
     * statusCodes={
     *         200="On success",
     *         400="On invalid parameters",
     *         401="On invalid authentication",
     *         503="On error reaching teamspeak"
     *     }
     * )
     * @RequestParam(name="ts_uuid", description="Teamspeak UUID")
     * @RequestParam(name="ts_code", description="Teamspeak Code")
     * @RequestParam(name="mc_uuid", description="Minecraft Username")
     * @RequestParam(name="mc_code", description="Minecraft Code")
     */
    public function apiV1AuthenticationsAction($ts_uuid, $ts_code, $mc_uuid, $mc_code)
    {
        $tsAccount = $this->checkTeamspeakCodeValid($ts_uuid, $ts_code);
        if(null == $tsAccount)
            throw new UnauthorizedHttpException('Invalid Teamspeak code supplied');

        $mcAccount = $this->checkMinecraftCodeValid($mc_uuid, $mc_code);
        if(null == $mcAccount)
            throw new UnauthorizedHttpException('Invalid Minecraft code supplied');

        //ALL CODES MATCHED, RUN THE PROCESS
        try {
            /** @var $tsHelper TeamspeakHelper */
            $tsHelper = $this->get('teamspeak_helper');
            $tsHelper->verifyClient($tsAccount, $mcAccount);
        } catch (TeamSpeak3_Exception $ex) {
            error_log($ex->getMessage());
            throw new ServiceUnavailableHttpException('There was a problem contacting the Teamspeak server');
        }
        return $this->view(null);
    }

    /**
     * @Route("/v1/authentications", name="api_v1_authentications_all")
     * @Method({"GET"})
     *
     * @ApiDoc(
     * section="Authentication",
     * description="Fetch a list of all the authentications, latest first",
     * tags={"API"},
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication",
     * statusCodes={
     *      200="On success",
     *      400="On invalid parameters"
     * }
     * )
     * @QueryParam(name="limit", description="Amount to return, max 50", requirements="\d+", default="10")
     * @QueryParam(name="offset", description="Offset to use", requirements="\d+", default="0")
     */
    public function apiV1AllAction($limit, $offset)
    {
        if ($limit > 50)
            throw new BadRequestHttpException('Only 50 authentications may be fetched per request');

        /** @var AuthenticationRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository('PublicUHCTeamspeakAuthBundle:Authentication');

        $results = $repo->findAllWithLimit($limit, $offset);

        return $this->view($results);
    }

    /**
     * @param $mc_uuid
     * @param $mc_code
     * @return null|MinecraftAccount account if valid, null otherwise
     */
    private function checkMinecraftCodeValid($mc_uuid, $mc_code)
    {
        /** @var MinecraftCodeRepository $mcCodeRepo */
        $mcCodeRepo = $this->getDoctrine()->getManager()->getRepository('PublicUHCTeamspeakAuthBundle:MinecraftCode');

        $mcCode = $mcCodeRepo->findOneByCodeWithinTime($mc_code, $this->container->getParameter('minutesToLast'));

        if(null == $mcCode)
            return null;

        /** @var $mcAccount MinecraftAccount */
        $mcAccount = $mcCode->getAccount();

        if($mcAccount->getName() != $mc_uuid)
            return null;

        return $mcAccount;
    }

    /**
     * @param $ts_uuid
     * @param $ts_code
     * @return null|TeamspeakAccount account if valid, null otherwise
     */
    private function checkTeamspeakCodeValid($ts_uuid, $ts_code)
    {
        /** @var TeamspeakCodeRepository $tsCodeRepo */
        $tsCodeRepo = $this->getDoctrine()->getManager()->getRepository('PublicUHCTeamspeakAuthBundle:TeamspeakCode');

        $tsCode = $tsCodeRepo->findOneByCodeWithinTime($ts_code, $this->container->getParameter('minutesToLast'));

        if(null == $tsCode)
            return null;

        /** @var $tsAccount TeamspeakAccount */
        $tsAccount = $tsCode->getAccount();

        if($tsAccount->getName() != $ts_uuid)
            return null;

        return $tsAccount;
    }
} 
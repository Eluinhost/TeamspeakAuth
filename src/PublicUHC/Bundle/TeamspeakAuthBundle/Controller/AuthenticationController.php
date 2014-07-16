<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TeamSpeak3_Exception;

/**
 * Class AuthenticationController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api")
 */
class AuthenticationController extends FOSRestController {

    /**
     * @Post("/v1/authentications", name="api_v1_authentications_new")
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
    public function api_v1_authenticationsAction($ts_uuid, $ts_code, $mc_uuid, $mc_code)
    {
        /** @var $entityManager EntityManager */
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $tsqb = $entityManager->createQueryBuilder();

        $tsqb->select('code')
            ->from('PublicUHCTeamspeakAuthBundle:TeamspeakCode', 'code')
            ->where(
                $tsqb->expr()->andX(
                    $tsqb->expr()->gt('code.updatedAt', ':timeago'),
                    $tsqb->expr()->eq('code.code', ':code')
                )
            )
            ->setMaxResults(1)
            ->orderBy('code.updatedAt', 'DESC')
            ->setParameter('timeago', new DateTime('-' . $this->container->getParameter('minutesToLast') . 'min'))
            ->setParameter('code', $ts_code);

        try {
            /** @var $tsCode TeamspeakCode */
            $tsCode = $tsqb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw new UnauthorizedHttpException('Invalid Teamspeak code supplied');
        }

        /** @var $tsAccount TeamspeakAccount */
        $tsAccount = $tsCode->getAccount();

        if($tsAccount->getUUID() != $ts_uuid) {
            throw new UnauthorizedHttpException('Invalid Teamspeak code supplied');
        }

        $mcqb = $entityManager->createQueryBuilder();

        $mcqb->select('code')
            ->from('PublicUHCTeamspeakAuthBundle:MinecraftCode', 'code')
            ->where(
                $tsqb->expr()->andX(
                    $tsqb->expr()->gt('code.updatedAt', ':timeago'),
                    $tsqb->expr()->eq('code.code', ':code')
                )
            )
            ->setMaxResults(1)
            ->orderBy('code.updatedAt', 'DESC')
            ->setParameter('timeago', new DateTime('-' . $this->container->getParameter('minutesToLast') . 'min'))
            ->setParameter('code', $mc_code);

        try {
            /** @var $mcCode MinecraftCode */
            $mcCode = $mcqb->getQuery()->getSingleResult();
        } catch(NoResultException $ex) {
            throw new UnauthorizedHttpException('Invalid Minecraft code supplied');
        }

        /** @var $mcAccount MinecraftAccount */
        $mcAccount = $mcCode->getAccount();

        if($mcAccount->getName() != $mc_uuid) {
            throw new UnauthorizedHttpException('Invalid Minecraft code supplied');
        }

        //ALL CODES MATCHED, RUN THE PROCESS
        try {
            /** @var $tsHelper TeamspeakHelper */
            $tsHelper = $this->get('teamspeak_helper');

            $tsHelper->verifyClient($tsAccount, $mcAccount);
        } catch ( TeamSpeak3_Exception $ex ) {
            error_log($ex->getMessage());
            throw new ServiceUnavailableHttpException('There was a problem contacting the Teamspeak server');
        }

        return $this->view(null, 200);
    }

    /**
     * @Get("/v1/authentications", name="api_v1_authentications_all")
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
    public function api_v1_allAction($limit, $offset)
    {
        if($limit > 50)
            throw new BadRequestHttpException('Only 50 authentications may be fetched per request');

        /** @var $entityManager EntityManager */
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $qb = $entityManager->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHCTeamspeakAuthBundle:Authentication', 'authentication')
            ->orderBy('authentication.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        return $this->view($results, 200);
    }
} 
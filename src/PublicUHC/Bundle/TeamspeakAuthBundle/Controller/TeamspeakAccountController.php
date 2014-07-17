<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\Route;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TeamspeakAccountController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api", defaults={"_format"="json"})
 */
class TeamspeakAccountController extends FOSRestController {

    /**
     * @Get("/v1/teamspeak_account", name="api_v1_teamspeak_account_list")
     *
     * @QueryParam(
     *  name="type",
     *  description="Search type. If 'online' will only return accounts with online teamspeak accounts. If 'verified' will only return accounts with verified accounts (with at least 1 authentication). If 'any' or missing will return all accounts",
     *  requirements="(online|verified|any)",
     *  default="any"
     * )
     * @QueryParam(name="uuids", description="Comma separated list of user UUIDs", nullable=true)
     * @QueryParam(name="limit", description="Limit amount returned, ignored if searching by UUIDs, max 50", requirements="\d+", default="10")
     * @QueryParam(name="offset", description="Offset, ignored if searching by UUIDs", requirements="\d+", default="0")
     *
     * @ApiDoc(
     * section="Teamspeak Accounts",
     * description="View teamspeak accounts",
     * tags={"API"},
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount",
     * statusCodes={
     *      200="On success",
     *      400="On invalid parameters",
     *      503="On unable to reach Teamspeak server (online checks only)"
     * }
     * )
     */
    public function api_v1_checkTeamspeakAccountAction($uuids, $type, $limit, $offset)
    {
        if($limit > 50)
            throw new BadRequestHttpException('Only 50 accounts may be fetched per request');

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $ex = $qb->expr();

        $qb->select('tsAccount')
            ->from('PublicUHCTeamspeakAuthBundle:TeamspeakAccount', 'tsAccount')
            ->leftJoin('tsAccount.authentications', 'authentication')
            ->leftJoin('authentication.minecraftAccount', 'mcAccount');

        if(null != $uuids) {
            $qb->where($ex->in('tsAccount.uuid', explode(',', $uuids)));
        } else {
            $qb->setMaxResults($limit);
            $qb->setFirstResult($offset);
        }

        if($type != 'any') {
            $qb->groupBy('tsAccount')->having($ex->gt($ex->count('authentication'), 0));
        }

        $results = $qb->getQuery()->getResult();

        if($type == 'online') {
            /** @var TeamspeakHelper $teamspeak_helper */
            $teamspeak_helper = $this->get('teamspeak_helper');

            $filteredResults = [];

            /** @var TeamspeakAccount $result */
            foreach($results as $result) {
                if($teamspeak_helper->isUUIDOnline($result->getUUID())) {
                    array_push($filteredResults, $result);
                }
            }

            $results = $filteredResults;
        }

        return $this->view($results);
    }
} 
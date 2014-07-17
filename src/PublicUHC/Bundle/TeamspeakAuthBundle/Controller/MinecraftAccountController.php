<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\Route;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class MinecraftAccountController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api", defaults={"_format"="json"})
 */
class MinecraftAccountController extends FOSRestController {

    /**
     * @Get("/v1/minecraft_account", name="api_v1_minecraft_account_list")
     *
     * @QueryParam(
     *  name="type",
     *  description="Search type. If 'online' will only return accounts with online teamspeak accounts. If 'verified' will only return accounts with verified accounts (with at least 1 authentication). If 'any' or missing will return all accounts",
     *  requirements="(online|verified|any)",
     *  default="any"
     * )
     * @QueryParam(name="uuids", description="Comma separated list of user UUIDs (without dashes)", nullable=true)
     * @QueryParam(name="limit", description="Limit amount returned, ignored if searching by UUIDs, max 50", requirements="\d+", default="10")
     * @QueryParam(name="offset", description="Offset, ignored if searching by UUIDs", requirements="\d+", default="0")
     *
     * @ApiDoc(
     * section="Minecraft Accounts",
     * description="View minecraft accounts",
     * tags={"API"},
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount",
     * statusCodes={
     *      200="On success",
     *      400="On invalid parameters",
     *      503="On unable to reach Teamspeak server (online checks only)"
     * }
     * )
     */
    public function api_v1_checkMinecraftAccountAction($uuids, $type, $limit, $offset)
    {
        if($limit > 50)
            throw new BadRequestHttpException('Only 50 accounts may be fetched per request');

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $ex = $qb->expr();

        $qb->select('mcAccount')
            ->from('PublicUHCTeamspeakAuthBundle:MinecraftAccount', 'mcAccount')
            ->leftJoin('mcAccount.authentications', 'authentication')
            ->leftJoin('authentication.teamspeakAccount', 'tsAccount');

        if(null != $uuids) {
            $qb->where($ex->in('mcAccount.uuid', explode(',', $uuids)));
        } else {
            $qb->setMaxResults($limit);
            $qb->setFirstResult($offset);
        }

        if($type != 'any') {
            $qb->groupBy('mcAccount')->having($ex->gt($ex->count('authentication'), 0));
        }

        $results = $qb->getQuery()->getResult();

        if($type == 'online') {
            /** @var TeamspeakHelper $teamspeak_helper */
            $teamspeak_helper = $this->get('teamspeak_helper');

            $filteredResults = [];

            /** @var MinecraftAccount $result */
            foreach($results as $result) {
                $auths = $result->getAuthentications();
                /** @var Authentication $auth */
                foreach($auths as $auth) {
                    if($teamspeak_helper->isUUIDOnline($auth->getTeamspeakAccount()->getUUID())) {
                        array_push($filteredResults, $result);
                        break;
                    }
                }
            }

            $results = $filteredResults;
        }

        return $this->view($results);
    }
}
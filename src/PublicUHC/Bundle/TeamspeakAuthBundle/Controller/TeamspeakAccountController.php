<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\Route;

/**
 * Class TeamspeakAccountController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api")
 */
class TeamspeakAccountController extends FOSRestController {

    /**
     * @Get("/v1/teamspeak_account", name="api_v1_teamspeak_account_list")
     *
     * @QueryParam(name="type", description="Search type", requirements="online|verified|any", default="any")
     * @QueryParam(name="uuids", description="Search by user UUID", array=true, nullable=true)
     * @QueryParam(name="limit", description="Limit amount returned, ignored if searching by UUIDs, max 50", default="10")
     * @QueryParam(name="offset", description="Offset, ignored if searching by UUIDs", default="0")
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
    public function api_v1_checkTeamspeakAccountAction(array $uuids, $online, $verified)
    {
        //TODO
    }
} 
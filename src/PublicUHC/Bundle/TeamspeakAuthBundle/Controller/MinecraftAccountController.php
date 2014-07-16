<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\Route;
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
     * @QueryParam(name="type", description="Search type", requirements="online|verified|any", default="any")
     * @QueryParam(name="uuids", description="Search by user UUID", array=true, nullable=true)
     * @QueryParam(name="limit", description="Limit amount returned, ignored if searching by UUIDs, max 50", default="10")
     * @QueryParam(name="offset", description="Offset, ignored if searching by UUIDs", default="0")
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
    public function api_v1_checkMinecraftAccountAction(array $uuids, $type, $limit, $offset)
    {
        return $this->view(func_get_args());
    }
} 
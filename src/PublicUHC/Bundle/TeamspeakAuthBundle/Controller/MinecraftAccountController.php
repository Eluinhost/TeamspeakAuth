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
 * @Route("/api")
 */
class MinecraftAccountController extends FOSRestController {

    /**
     * @Get("/v1/minecraft_account", name="api_v1_minecraft_account_list")
     *
     * @QueryParam(name="verified", description="Only return accounts with authentications", default=false)
     * @QueryParam(name="online", description="Only return accounts with an online teamspeak account", default=false)
     * @QueryParam(name="uuids", description="Search by user UUID", array=true, nullable=true)
     *
     * @ApiDoc(
     * description="View minecraft accounts",
     * tags={"API"},
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount"
     * )
     */
    public function api_v1_checkMinecraftAccountAction(array $uuids, $online, $verified)
    {
        //TODO
    }
} 
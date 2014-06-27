<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TeamspeakAccountController extends FOSRestController {

    /**
     * @Get("/v1/teamspeak_account.{_format}", name="api_v1_teamspeak_account_list")
     *
     * @QueryParam(name="verified", description="Only return accounts with authentications", default=false)
     * @QueryParam(name="online", description="Only return accounts with an online teamspeak account", default=false)
     * @QueryParam(name="uuids", description="Search by user UUID", array=true, nullable=true)
     *
     * @ApiDoc(
     * description="Output an array of teamspeak accounts",
     * tags={"api"},
     * requirements={
     *      {
     *          "name"="_format",
     *          "dataType"="String",
     *          "requirement"="json|xml",
     *          "description"="Format of response, if empty will be JSON"
     *      }
     * },
     * output="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount"
     * )
     */
    public function api_v1_checkTeamspeakAccountAction(array $uuids, $online, $verified)
    {
        //TODO
    }
} 
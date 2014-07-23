<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccountRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use PublicUHC\Bundle\TeamspeakAuthBundle\Model\AccountSearchParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class MinecraftAccountController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * @Route("/api", defaults={"_format"="json"})
 */
class MinecraftAccountController extends FOSRestController
{

    /**
     * @Get("/v1/minecraft_account", name="api_v1_minecraft_account_list")
     * @Method({"GET"})
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
    public function apiV1CheckMinecraftAccountAction($uuids, $type, $limit, $offset)
    {
        if ($limit > 50)
            throw new BadRequestHttpException('Only 50 accounts may be fetched per request');

        $params = new AccountSearchParameters();

        if (null != $uuids)
            $params->setUuids($uuids);

        if ($type != 'any')
            $params->setVerified(true);

        $params->setLimit($limit);
        $params->setOffset($offset);

        /** @var MinecraftAccountRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository('PublicUHCTeamspeakAuthBundle:MinecraftAccount');

        $results = $repo->findAllByParameters($params);

        if ($type == 'online') {
            $results = $this->filterOnlineOnly($results);
        }

        return $this->view($results);
    }

    private function filterOnlineOnly($accountArray)
    {
        /** @var TeamspeakHelper $teamspeak_helper */
        $teamspeak_helper = $this->get('teamspeak_helper');

        $filteredResults = [];

        /** @var MinecraftAccount $result */
        foreach ($accountArray as $result) {
            $auths = $result->getAuthentications();
            /** @var Authentication $auth */
            foreach ($auths as $auth) {
                if ($teamspeak_helper->isUUIDOnline($auth->getTeamspeakAccount()->getUUID())) {
                    array_push($filteredResults, $result);
                    break;
                }
            }
        }

        return $filteredResults;
    }
}
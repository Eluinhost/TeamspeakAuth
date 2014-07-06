<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIController
 * @package PublicUHC\Bundle\TeamspeakAuthBundle\Controller
 *
 * TODO REMOVE
 */
class APIController extends Controller {

    /**
     * @Route("/api/verified", defaults={"online"=false})
     * @Route("/api/online", defaults={"online"=true})
     *
     * @param Request $request
     * @param $online
     * @return JsonResponse
     */
    public function checkVerified(Request $request, $online)
    {
        $response = new JsonResponse();

        if( !$request->request->has('uuids') || !is_array($request->request->get('uuids'))) {
            $response->setStatusCode(400);
            $response->setData([
                'error' => 'Must provide an array of UUIDs in POST data'
            ]);
            return $response;
        }

        $uuids = $request->request->get('uuids');

        $status = [];

        /** @var $entityManager EntityManager */
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /** @var $teamspeakHelper TeamspeakHelper */
        $teamspeakHelper = $this->get('teamspeak_helper');

        foreach($uuids as $uuid) {
            $uuidStatus = [];

            $qb = $entityManager->createQueryBuilder();

            $qb->select('authentication')
                ->from('PublicUHCTeamspeakAuthBundle:Authentication', 'authentication')
                ->join('authentication.minecraftAccount', 'mcAccount')
                ->where('mcAccount.uuid = :uuid')
                ->setParameter('uuid', $uuid);

            $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

            if (count($results) == 0) {
                $status[$uuid] = false;
                continue;
            }

            //all authentications will have the same account
            /** @var $mcAccount MinecraftAccount */
            $mcAccount = $results[0]->getMinecraftAccount();
            $uuidStatus['minecraftAccount'] = [
                'createdAt' => $mcAccount->getCreatedAt()->getTimestamp(),
                'updatedAt' => $mcAccount->getUpdatedAt()->getTimestamp(),
                'uuid'      => $mcAccount->getUUID(),
                'lastName'  => $mcAccount->getName()
            ];

            $authentications = [];

            /** @var $authentication Authentication */
            foreach ($results as $authentication) {
                $tsAccount = $authentication->getTeamspeakAccount();

                $authenticationJSON = [
                    'createdAt' => $authentication->getCreatedAt()->getTimestamp(),
                    'updatedAt' => $authentication->getUpdatedAt()->getTimestamp(),
                    'teamspeakAccount' => [
                        'createdAt' => $tsAccount->getCreatedAt()->getTimestamp(),
                        'updatedAt' => $tsAccount->getUpdatedAt()->getTimestamp(),
                        'uuid' => $tsAccount->getUUID(),
                        'lastName' => $tsAccount->getName(),
                        'online' => false
                    ]
                ];

                if ($online && $teamspeakHelper->isUUIDOnline($tsAccount->getUUID())) {
                    $authenticationJSON['teamspeakAccount']['online'] = true;
                }

                array_push($authentications, $authenticationJSON);
            }

            $uuidStatus['authentications'] = $authentications;

            $status[$uuid] = $uuidStatus;
        }

        $response->setData($status);
        return $response;
    }
} 
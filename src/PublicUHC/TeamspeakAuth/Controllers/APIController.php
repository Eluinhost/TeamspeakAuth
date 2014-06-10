<?php
namespace PublicUHC\TeamspeakAuth\Controllers;


use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use PublicUHC\TeamspeakAuth\Entities\Authentication;
use PublicUHC\TeamspeakAuth\Entities\MinecraftAccount;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class APIController extends ContainerAware {

    public function checkVerified(Request $request, $checkOnline)
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
        $entityManager = $this->container->get('entityManager');
        /** @var $teamspeakHelper TeamspeakHelper */
        $teamspeakHelper = $this->container->get('teamspeakhelper');

        foreach($uuids as $uuid) {
            $uuidStatus = [];

            $qb = $entityManager->createQueryBuilder();

            $qb->select('authentication')
                ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
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

                if ($checkOnline && $teamspeakHelper->isUUIDOnline($tsAccount->getUUID())) {
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
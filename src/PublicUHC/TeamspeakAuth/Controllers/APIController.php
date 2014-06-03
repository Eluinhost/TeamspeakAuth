<?php
namespace PublicUHC\TeamspeakAuth\Controllers;


use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use PublicUHC\TeamspeakAuth\Entities\Authentication;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIController extends ContainerAware {

    public function checkVerified($mc_uuid, $checkOnline)
    {
        $response = new JsonResponse();

        /** @var $entityManager EntityManager */
        $entityManager = $this->container->get('entityManager');

        $qb = $entityManager->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
            ->join('authentication.minecraftAccount', 'mcAccount')
            ->where('mcAccount.uuid = :uuid')
            ->setParameter('uuid', $mc_uuid);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        if(count($results) == 0) {
            $response->setData([
                'verified' => false
            ]);
            return $response;
        }

        /** @var $teamspeakHelper TeamspeakHelper */
        $teamspeakHelper = $this->container->get('teamspeakhelper');

        $authenticationsJson = [];
        $online = [];

        /** @var $authentication Authentication */
        foreach($results as $authentication) {
            $tsAccount = $authentication->getTeamspeakAccount();
            $mcAccount = $authentication->getMinecraftAccount();

            array_push($authenticationsJson, [
                'createdAt' => $authentication->getCreatedAt()->format(DateTime::RFC2822),
                'updatedAt' => $authentication->getUpdatedAt()->format(DateTime::RFC2822),
                'minecraftAccount' => [
                    'createdAt' => $mcAccount->getCreatedAt()->format(DateTime::RFC2822),
                    'updatedAt' => $mcAccount->getUpdatedAt()->format(DateTime::RFC2822),
                    'uuid' => $mcAccount->getUUID()
                ],
                'teamspeakAccount' => [
                    'createdAt' => $tsAccount->getCreatedAt()->format(DateTime::RFC2822),
                    'updatedAt' => $tsAccount->getUpdatedAt()->format(DateTime::RFC2822),
                    'uuid' => $tsAccount->getUUID()
                ]
            ]);
            if($checkOnline) {
                if($teamspeakHelper->isUUIDOnline($tsAccount->getUUID())) {
                    array_push($online, $tsAccount->getUUID());
                }
            }
        }

        $returnData = [
            'verified' => true,
            'authentications' => $authenticationsJson
        ];

        if($checkOnline) {
            $returnData['online'] = $online;
        }

        $response->setData($returnData);
        return $response;
    }
} 
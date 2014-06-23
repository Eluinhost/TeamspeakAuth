<?php
namespace PublicUHC\TeamspeakAuth\Controllers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use PublicUHC\TeamspeakAuth\Entities\Authentication;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthenticationController extends ContainerAware {

    public function authenticationsAction($limit, $offset) {
        if($limit > 50)
            throw new BadRequestHttpException('Only 50 authentications may be fetched per request');

        /** @var $entityManager EntityManager */
        $entityManager = $this->container->get('entityManager');

        $qb = $entityManager->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
            ->orderBy('authentication.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);

        $returnArray = [];
        /** @var $result Authentication */
        foreach($results as $result) {
            $tsAccount = $result->getTeamspeakAccount();
            $mcAccount = $result->getMinecraftAccount();
            array_push($returnArray,
                [
                    'updatedAt' => $result->getUpdatedAt()->getTimestamp(),
                    'createdAt' => $result->getCreatedAt()->getTimestamp(),
                    'teamspeakAccount' => [
                        'createdAt' => $tsAccount->getCreatedAt()->getTimestamp(),
                        'updatedAt' => $tsAccount->getUpdatedAt()->getTimestamp(),
                        'uuid' => $tsAccount->getUUID(),
                        'lastName' => $tsAccount->getName()
                    ],
                    'minecraftAccount' => [
                        'createdAt' => $mcAccount->getCreatedAt()->getTimestamp(),
                        'updatedAt' => $mcAccount->getUpdatedAt()->getTimestamp(),
                        'uuid'      => $mcAccount->getUUID(),
                        'lastName'  => $mcAccount->getName(),
                        'skin' =>
                            $this->container->get('router')->generate(
                                'avatarhelm',
                                ['username' => $mcAccount->getName()]
                            )
                    ]
                ]
            );
        }

        return new JsonResponse($returnArray);
    }
} 
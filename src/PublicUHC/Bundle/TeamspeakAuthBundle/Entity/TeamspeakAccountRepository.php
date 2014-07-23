<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\ORM\EntityRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Model\AccountSearchParameters;

/**
 * TeamspeakAccountRepository
 */
class TeamspeakAccountRepository extends EntityRepository
{

    public function findAllByParameters(AccountSearchParameters $parameters)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $ex = $qb->expr();

        $qb->select('tsAccount')
            ->from('PublicUHCTeamspeakAuthBundle:TeamspeakAccount', 'tsAccount')
            ->leftJoin('tsAccount.authentications', 'authentication')
            ->leftJoin('authentication.minecraftAccount', 'mcAccount');

        if(count($parameters->getUuids()) > 0) {
            $qb->where($ex->in('tsAccount.uuid', explode(',', $parameters->getUuids())));
        } else {
            $qb->setMaxResults($parameters->getLimit());
            $qb->setFirstResult($parameters->getOffset());
        }

        if($parameters->isVerified()) {
            $qb->groupBy('tsAccount')->having($ex->gt($ex->count('authentication'), 0));
        }

        return $qb->getQuery()->getResult();
    }
}

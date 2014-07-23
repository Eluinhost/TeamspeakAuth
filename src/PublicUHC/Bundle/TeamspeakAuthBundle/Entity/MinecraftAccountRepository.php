<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\ORM\EntityRepository;
use PublicUHC\Bundle\TeamspeakAuthBundle\Model\AccountSearchParameters;

/**
 * MinecraftAccountRepository
 */
class MinecraftAccountRepository extends EntityRepository
{
    /**
     * Parameters:
     *
     * uuids - list of uuids to search for, if empty array returns all
     * limit - amount to return, ignored if using uuids
     * offset - offset in dataset, ignored if using uuids
     * verified - true for authed accounts only
     *
     * @param AccountSearchParameters $parameters
     * @return array
     */
    public function findAllByParameters(AccountSearchParameters $parameters)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $ex = $qb->expr();

        $qb->select('mcAccount')
            ->from('PublicUHCTeamspeakAuthBundle:MinecraftAccount', 'mcAccount')
            ->leftJoin('mcAccount.authentications', 'authentication')
            ->leftJoin('authentication.teamspeakAccount', 'tsAccount');

        if(count($parameters->getUuids()) > 0) {
            $qb->where($ex->in('mcAccount.uuid', explode(',', $parameters->getUuids())));
        } else {
            $qb->setMaxResults($parameters->getLimit());
            $qb->setFirstResult($parameters->getOffset());
        }

        if($parameters->isVerified()) {
            $qb->groupBy('mcAccount')->having($ex->gt($ex->count('authentication'), 0));
        }

        return $qb->getQuery()->getResult();
    }
}

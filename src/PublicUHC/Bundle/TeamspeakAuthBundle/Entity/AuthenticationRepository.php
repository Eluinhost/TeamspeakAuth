<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AuthenticationRepository
 */
class AuthenticationRepository extends EntityRepository
{
    public function findAllWithLimit($limit, $offset = 0)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHCTeamspeakAuthBundle:Authentication', 'authentication')
            ->orderBy('authentication.updatedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }
}

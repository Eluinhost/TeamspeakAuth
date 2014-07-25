<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * TeamspeakCodeRepository
 */
class TeamspeakCodeRepository extends EntityRepository
{
    /**
     * Find a single result that matches
     * @param $code string the code to look for
     * @param $time int the number of minutes it has to have been updated within
     * @return TeamspeakCode|null code or null if none found
     */
    public function findOneByCodeWithinTime($code, $time)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $ex = $qb->expr();

        $qb->select('code')
            ->from('PublicUHCTeamspeakAuthBundle:TeamspeakCode', 'code')
            ->join('code.account', 'account')
            ->where(
                $ex->andX(
                    $ex->gt('code.updatedAt', ':timeago'),
                    $ex->eq('code.code', ':code')
                )
            )
            ->setMaxResults(1)
            ->orderBy('code.updatedAt', 'DESC')
            ->setParameter('timeago', new DateTime('-' . $time . 'min'))
            ->setParameter('code', $code);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }
}

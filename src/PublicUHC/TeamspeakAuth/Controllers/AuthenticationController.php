<?php
namespace PublicUHC\TeamspeakAuth\Controllers;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use PublicUHC\TeamspeakAuth\Entities\Authentication;
use PublicUHC\TeamspeakAuth\Entities\MinecraftAccount;
use PublicUHC\TeamspeakAuth\Entities\MinecraftCode;
use PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount;
use PublicUHC\TeamspeakAuth\Entities\TeamspeakCode;
use PublicUHC\TeamspeakAuth\Helpers\TeamspeakHelper;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use TeamSpeak3_Exception;

class AuthenticationController extends ContainerAware {

    public function authenticateAction(Request $request) {
        $ts_uuid = $request->request->get('ts_uuid');
        $ts_code = $request->request->get('ts_code');
        $mc_uuid = $request->request->get('mc_uuid');
        $mc_code = $request->request->get('mc_code');

        if($ts_uuid == null)
            throw new BadRequestHttpException('Must provide a Teamspeak UUID');
        if($ts_code == null)
            throw new BadRequestHttpException('Must provide a Teamspeak code');
        if($mc_uuid == null)
            throw new BadRequestHttpException('Must provide a Minecraft UUID');
        if($mc_code == null)
            throw new BadRequestHttpException('Must provide a Minecraft code');

        /** @var $entityManager EntityManager */
        $entityManager = $this->container->get('entityManager');

        $tsqb = $entityManager->createQueryBuilder();

        $tsqb->select('code')
            ->from('PublicUHC\TeamspeakAuth\Entities\TeamspeakCode', 'code')
            ->where(
                $tsqb->expr()->andX(
                    $tsqb->expr()->gt('code.updatedAt', ':timeago'),
                    $tsqb->expr()->eq('code.code', ':code')
                )
            )
            ->setMaxResults(1)
            ->orderBy('code.updatedAt', 'DESC')
            ->setParameter('timeago', new DateTime('-' . $this->container->getParameter('minutesToLast') . 'min'))
            ->setParameter('code', $ts_code);

        try {
            /** @var $tsCode TeamspeakCode */
            $tsCode = $tsqb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw new BadRequestHttpException('Invalid Teamspeak code supplied');
        }

        /** @var $tsAccount TeamspeakAccount */
        $tsAccount = $tsCode->getAccount();

        if($tsAccount->getUUID() != $ts_uuid) {
            throw new BadRequestHttpException('Invalid Teamspeak code supplied');
        }

        $mcqb = $entityManager->createQueryBuilder();

        $mcqb->select('code')
            ->from('PublicUHC\TeamspeakAuth\Entities\MinecraftCode', 'code')
            ->where(
                $tsqb->expr()->andX(
                    $tsqb->expr()->gt('code.updatedAt', ':timeago'),
                    $tsqb->expr()->eq('code.code', ':code')
                )
            )
            ->setMaxResults(1)
            ->orderBy('code.updatedAt', 'DESC')
            ->setParameter('timeago', new DateTime('-' . $this->container->getParameter('minutesToLast') . 'min'))
            ->setParameter('code', $mc_code);

        try {
            /** @var $mcCode MinecraftCode */
            $mcCode = $mcqb->getQuery()->getSingleResult();
        } catch(NoResultException $ex) {
            throw new BadRequestHttpException('Invalid Minecraft code supplied');
        }

        /** @var $mcAccount MinecraftAccount */
        $mcAccount = $mcCode->getAccount();

        if($mcAccount->getName() != $mc_uuid) {
            throw new BadRequestHttpException('Invalid Minecraft code supplied');
        }

        //ALL CODES MATCHED, RUN THE PROCESS
        try {
            /** @var $tsHelper TeamspeakHelper */
            $tsHelper = $this->container->get('teamspeakhelper');

            $tsHelper->verifyClient($tsAccount, $mcAccount);
            return new JsonResponse();
        } catch ( TeamSpeak3_Exception $ex ) {
            error_log($ex->getMessage());
            throw new BadRequestHttpException('There was a problem contacting Teamspeak');
        }
    }

    public function authenticationsAction($limit, $offset) {
        /** @var $request Request */
        $request = $this->container->get('request');
        $limit = $request->query->getInt('limit', $limit);
        $offset = $request->query->getInt('offset', $offset);

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
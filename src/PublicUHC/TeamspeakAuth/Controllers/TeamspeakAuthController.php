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
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamspeakAuthController extends ContainerAware {

    public function authAction(Request $request) {
        $response = new JsonResponse();

        $ts_uuid = $request->query->get('ts_uuid');
        $ts_code = $request->query->get('ts_code');
        $mc_uuid = $request->query->get('mc_uuid');
        $mc_code = $request->query->get('mc_code');

        try {

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
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid Teamspeak code supplied'
                ]);
                return $response;
            }

            /** @var $tsAccount TeamspeakAccount */
            $tsAccount = $tsCode->getAccount();

            if($tsAccount->getUUID() != $ts_uuid) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid code for the given Teamspeak account'
                ]);
                return $response;
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
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid Minecraft code supplied'
                ]);
                return $response;
            }

            /** @var $mcAccount MinecraftAccount */
            $mcAccount = $mcCode->getAccount();

            if($mcAccount->getName() != $mc_uuid) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Invalid code for the given Minecraft account'
                ]);
                return $response;
            }

            //ALL CODES MATCHED, RUN THE PROCESS

            /** @var $tsHelper TeamspeakHelper */
            $tsHelper = $this->container->get('teamspeakhelper');

            try {
                $tsHelper->verifyClient($tsAccount, $mcAccount);
            } catch (Exception $ex) {
                error_log($ex->getMessage());
                error_log($ex->getTraceAsString());
            }
            return $response;
        } catch ( \PDOException $ex ) {
            error_log($ex->getMessage());
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error connecting to the database'
            ]);
            return $response;
        } catch ( \TeamSpeak3_Exception $ex ) {
            error_log($ex->getMessage());
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'There was a problem with Teamspeak, please try again later'
            ]);
            return $response;
        }
    }

    public function requestTeamspeakAction(Request $request) {
        $response = new JsonResponse();

        $tsName = $request->query->get('ts_name');

        if( $tsName == null || strlen($tsName) == 0 || strlen($tsName) > 30 ) {
            $response->setStatusCode(400);
            $response->setData([
                'error' => 'Invalid teamspeak name provided'
            ]);
            return $response;
        }

        /**
         * @var $ts3 TeamspeakHelper
         */
        $ts3 = $this->container->get('teamspeakhelper');

        try {

            $client = $ts3->getClientForName($tsName);

            if (null == $client) {
                $response->setStatusCode(400);
                $response->setData([
                    'error' => 'Teamspeak user not found'
                ]);
                return $response;
            }

            $uuid = $ts3->getUUIDForClient($client);

            $account = $ts3->updateLastClientUsername($client);

            $code = new TeamspeakCode();
            $account->getCodes()->clear();
            $account->getCodes()->add($code);
            $code->setAccount($account);

            /** @var $entityManager EntityManager */
            $entityManager = $this->container->get('entityManager');
            $entityManager->persist($code);
            $entityManager->persist($account);
            $entityManager->flush();

            $timeToLast = $this->container->getParameter('minutesToLast');
            $codeString = $code->getCode();
            $client->message("[Verification Code] AUTH CODE: '{$codeString}'. This code work for the next {$timeToLast} minutes");

            $response->setData([
                'UUID' => $uuid
            ]);
            return $response;

        } catch ( \TeamSpeak3_Exception $ignored ) {
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error contacting the teamspeak server'
            ]);
            return $response;
        } catch ( \PDOException $ex) {
            $response->setStatusCode(500);
            $response->setData([
                'error' => 'Error connecting to the database'
            ]);
            return $response;
        }
    }

    public function latestAuthsAction(Request $request) {
        $response = new JsonResponse();

        /** @var $entityManager EntityManager */
        $entityManager = $this->container->get('entityManager');

        $qb = $entityManager->createQueryBuilder();

        $qb->select('authentication')
            ->from('PublicUHC\TeamspeakAuth\Entities\Authentication', 'authentication')
            ->orderBy('authentication.updatedAt', 'DESC')
            ->setMaxResults(10);

        $returnArray = [];

        $results = $qb->getQuery()->getResult(Query::HYDRATE_OBJECT);
        /** @var $result Authentication */
        foreach($results as $result) {
            array_push($returnArray, [
                'updatedAt' => $result->getUpdatedAt()->format(DateTime::RFC2822),
                'createdAt' => $result->getCreatedAt()->format(DateTime::RFC2822),
                'ts_name' => $result->getTeamspeakAccount()->getName(),
                'mc_name' => $result->getMinecraftAccount()->getName(),
                'image_url' => $this->container->get('router')->generate('avatarhelm', ['username' => $result->getMinecraftAccount()->getName()])
            ]);
        }

        $response->setData($returnArray);

        return $response;
    }

    public function indexAction() {
        $templating = $this->container->get('templating');
        return new Response($templating->render('app.html.haml'));
    }

    public function teamspeakRequestPageAction() {
        $templating = $this->container->get('templating');
        return new Response($templating->render('teamspeakrequest.html.haml'));
    }

    public function verifyAccountPageAction($uuid) {
        if(null == $uuid) {
            throw new NotFoundHttpException();
        }

        $templating = $this->container->get('templating');
        return new Response($templating->render('accountverification.html.haml',
            [
                'ts_uuid' => $uuid,
                'serverAddress' => $this->container->getParameter('serverAddress')
            ]
        ));
    }

    public function completeAction($mc_name) {
        if(null == $mc_name) {
            throw new NotFoundHttpException();
        }

        $templating = $this->container->get('templating');
        return new Response($templating->render('complete.html.haml',
            [
                'mc_name' => $mc_name
            ]
        ));
    }
} 
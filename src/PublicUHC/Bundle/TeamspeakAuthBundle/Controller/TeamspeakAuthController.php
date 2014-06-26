<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount;
use PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode;
use PublicUHC\Bundle\TeamspeakAuthBundle\Helpers\TeamspeakHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TeamspeakAuthController extends Controller {

    /**
     * @Route("/auth")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function authAction(Request $request) {
        $response = new JsonResponse();

        $ts_uuid = $request->query->get('ts_uuid');
        $ts_code = $request->query->get('ts_code');
        $mc_uuid = $request->query->get('mc_uuid');
        $mc_code = $request->query->get('mc_code');

        try {

            /** @var $entityManager EntityManager */
            $entityManager = $this->get('doctrine.orm.entity_manager');

            $tsqb = $entityManager->createQueryBuilder();

            $tsqb->select('code')
                ->from('PublicUHCTeamspeakAuthBundle:TeamspeakCode', 'code')
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
                ->from('PublicUHCTeamspeakAuthBundle:MinecraftCode', 'code')
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
            $tsHelper = $this->get('teamspeak_helper');

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

    /**
     * @Route("/teamspeakcode")
     *
     * @param Request $request
     * @return JsonResponse
     */
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
        $ts3 = $this->get('teamspeak_helper');

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
            $entityManager = $this->get('doctrine.orm.entity_manager');
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

    /**
     * @Route("/")
     * @return Response
     */
    public function indexAction() {
        $templating = $this->get('templating');
        return new Response($templating->render('PublicUHCTeamspeakAuthBundle:TeamspeakAuth:app.html.haml'));
    }

    /**
     * @Route("/teamspeakrequest")
     * @return Response
     */
    public function teamspeakRequestPageAction() {
        $templating = $this->container->get('templating');
        return new Response($templating->render('teamspeakrequest.html.haml'));
    }

    /**
     * @Route("/verifyaccount/{uuid}", requirements={"uuid"=".+"})
     *
     * @param $uuid
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
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

    /**
     * @Route("/complete/{mc_name}")
     *
     * @param $mc_name
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
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
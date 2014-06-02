<?php
namespace PublicUHC\TeamspeakAuth\Controllers;


use PublicUHC\SkinCache\Downloaders\MinotarLikeDownloader;
use PublicUHC\SkinCache\Formatters\HttpResponseFormatter;
use PublicUHC\SkinCache\SkinFetcher;
use Stash\Driver\BlackHole;
use Stash\Pool;
use Symfony\Component\DependencyInjection\ContainerAware;
use GuzzleHttp\Client;

class SkinController extends ContainerAware {

    public function helmAction($username, $size) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->container->get('skinfetcher');

        return $fetcher->fetchHelm($username, $size);
    }

    public function headAction($username, $size) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->container->get('skinfetcher');

        return $fetcher->fetchHead($username, $size);
    }

    public function skinAction($username) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->container->get('skinfetcher');

        return $fetcher->fetchSkin($username);
    }
}
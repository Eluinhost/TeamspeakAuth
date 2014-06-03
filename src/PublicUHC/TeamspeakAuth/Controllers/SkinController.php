<?php
namespace PublicUHC\TeamspeakAuth\Controllers;

use PublicUHC\SkinCache\SkinFetcher;
use Symfony\Component\DependencyInjection\ContainerAware;

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
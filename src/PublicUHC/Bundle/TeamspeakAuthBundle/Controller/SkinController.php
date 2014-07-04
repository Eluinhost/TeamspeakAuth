<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Controller;

use PublicUHC\SkinCache\SkinFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/skins")
 */
class SkinController extends Controller {

    /**
     * @Route("/helm/{username}/{size}", name="skin_helm", defaults={"size" = 16})
     */
    public function helmAction($username, $size) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->get('skin_fetcher');

        return $fetcher->fetchHelm($username, $size);
    }

    /**
     * @Route("/head/{username}/{size}", name="skin_head", defaults={"size" = 16})
     */
    public function headAction($username, $size) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->get('skin_fetcher');

        return $fetcher->fetchHead($username, $size);
    }

    /**
     * @Route("/skin/{username}", name="skin_skin")
     */
    public function skinAction($username) {
        /** @var $fetcher SkinFetcher */
        $fetcher = $this->get('skin_fetcher');

        return $fetcher->fetchSkin($username);
    }
}
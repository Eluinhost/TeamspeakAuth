<?php
namespace PublicUHC\TeamspeakAuth\Helpers;


use PublicUHC\SkinCache\SkinFetcher;

class DefaultMinecraftHelper implements MinecraftHelper {

    private $skinFetcher;

    public function __construct(SkinFetcher $skinFetcher) {
        $this->skinFetcher = $skinFetcher;
    }

    public function getIconForUsername($username)
    {
        return $this->skinFetcher->fetchHelm($username, 16);
    }
}
<?php
namespace PublicUHC\TeamspeakAuth\Helpers;


class DefaultMinecraftHelper implements MinecraftHelper {

    private $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function getIconForUsername($username)
    {
        $iconURL = str_replace(";username;", $username, $this->url);
        return file_get_contents($iconURL);
    }
}
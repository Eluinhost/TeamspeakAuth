<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Helpers;


interface MinecraftHelper {

    /**
     * Get the image of the head of the player provided
     * @param $username string the player's username
     * @return string the data or false if failed
     */
    public function getIconForUsername($username);

} 
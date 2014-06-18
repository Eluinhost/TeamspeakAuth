<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity
 * @Table
 */
class Authentication extends DatabaseObject {

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount", inversedBy="authentications")
     */
    private $teamspeakAccount;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\MinecraftAccount", inversedBy="authentications")
     */
    private $minecraftAccount;

    /**
     * @return TeamspeakAccount the teamspeak account associated with this authentication
     */
    public function getTeamspeakAccount() {
        return $this->teamspeakAccount;
    }

    /**
     * @param TeamspeakAccount $account the teamspeak account to associate with this authentication
     * @return Authentication
     */
    public function setTeamspeakAccount(TeamspeakAccount $account) {
        $this->teamspeakAccount = $account;
        return $this;
    }

    /**
     * @return MinecraftAccount the Minecraft account associated with this authentication
     */
    public function getMinecraftAccount() {
        return $this->minecraftAccount;
    }

    /**
     * @param MinecraftAccount $account the Minecraft account to associate with this authentication
     * @return $this
     */
    public function setMinecraftAccount(MinecraftAccount $account) {
        $this->minecraftAccount = $account;
        return $this;
    }
} 
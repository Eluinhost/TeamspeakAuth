<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @Entity
 * @Table(name="authentications")
 */
class Authentication {

    use DatabaseTrait;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount", inversedBy="authentications")
     * @JoinColumn(name="TeamspeakAccountId")
     */
    private $teamspeakAccount;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\MinecraftAccount", inversedBy="authentications")
     * @JoinColumn(name="MinecraftAccountId")
     */
    private $minecraftAccount;

    /**
     * @return TeamspeakAccount
     */
    public function getTeamspeakAccount() {
        return $this->teamspeakAccount;
    }

    public function setTeamspeakAccount(TeamspeakAccount $account) {
        $this->teamspeakAccount = $account;
        return $this;
    }

    /**
     * @return MinecraftAccount
     */
    public function getMinecraftAccount() {
        return $this->minecraftAccount;
    }

    public function setMinecraftAccount(MinecraftAccount $account) {
        $this->minecraftAccount = $account;
        return $this;
    }
} 
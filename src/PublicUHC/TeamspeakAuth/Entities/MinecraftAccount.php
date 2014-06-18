<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table
 */
class MinecraftAccount extends DatabaseObject {

    /**
     * @Column(type="string", length=32)
     */
    private $uuid;

    /**
     * @Column(type="string", length=16)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="PublicUHC\TeamspeakAuth\Entities\MinecraftCode", mappedBy="account", orphanRemoval=true)
     * @var $codes ArrayCollection
     */
    private $codes;

    /**
     * @OneToMany(targetEntity="PublicUHC\TeamspeakAuth\Entities\Authentication", mappedBy="minecraftAccount")
     */
    private $authentications;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
        $this->authentications = new ArrayCollection();
    }

    /**
     * @return ArrayCollection the list of authentications that have been made against this account
     */
    public function getAuthentications() {
        return $this->authentications;
    }

    /**
     * @return String the UUID of the Minecraft account without the -
     */
    public function getUUID() {
        return $this->uuid;
    }

    /**
     * @param $uuid String the UUID of the Minecraft account without the -
     * @return MinecraftAccount
     */
    public function setUUID($uuid) {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return String the username of the account when it was verified
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name String the username of the account when it was verified
     * @return MinecraftAccount
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection list of MinecraftCodes for this account (should only ever be one)
     */
    public function getCodes() {
        return $this->codes;
    }
}
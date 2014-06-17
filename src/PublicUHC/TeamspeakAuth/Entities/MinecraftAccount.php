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
 * @HasLifecycleCallbacks
 */
class MinecraftAccount {

    use DatabaseTrait;

    /**
     * @Column(type="guid")
     */
    private $uuid;

    /**
     * @Column(type="string")
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

    public function getAuthentications() {
        return $this->authentications;
    }

    public function getUUID() {
        return $this->uuid;
    }

    public function setUUID($uuid) {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getCodes() {
        return $this->codes;
    }
}
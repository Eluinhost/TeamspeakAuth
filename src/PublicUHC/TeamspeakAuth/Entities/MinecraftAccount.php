<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table(name="minecraftaccounts")
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

    public function __construct()
    {
        $this->codes = new ArrayCollection();
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
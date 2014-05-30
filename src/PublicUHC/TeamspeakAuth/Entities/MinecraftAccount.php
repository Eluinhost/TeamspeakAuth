<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;

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

    /**
     * @ManyToMany(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount", inversedBy="minecraftAccounts")
     * @JoinTable(name="authentications",
     *      joinColumns={@JoinColumn(name="TeamspeakAccountId")},
     *      inverseJoinColumns={@JoinColumn(name="MinecraftAccountId")}
     */
    private $teamspeakAccounts;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
        $this->teamspeakAccounts = new ArrayCollection();
    }

    public function getTeamspeakAccounts() {
        return $this->teamspeakAccounts;
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
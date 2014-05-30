<?php

namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity
 * @Table(name="teamspeakaccounts")
 */
class TeamspeakAccount {

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
     * @OneToMany(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakCode", mappedBy="account", orphanRemoval=true)
     * @var $codes ArrayCollection
     */
    private $codes;

    /**
     * @ManyToMany(targetEntity="PublicUHC\TeamspeakAuth\Entities\MinecraftAccount", inversedBy="teamspeakAccounts")
     * @JoinTable(name="authentications",
     *      joinColumns={@JoinColumn(name="MinecraftAccountId")},
     *      inverseJoinColumns={@JoinColumn(name="TeamspeakAccountId")})
     */
    private $minecraftAccounts;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
        $this->minecraftAccounts = new ArrayCollection();
    }

    public function getMinecraftAccounts() {
        return $this->minecraftAccounts;
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
<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * TeamspeakAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccountRepository")
 */
class TeamspeakAccount extends TimestampedEntity
{
    /**
     * @Column(type="string", length=28)
     */
    private $uuid;

    /**
     * @Column(type="string", length=30)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakCode", mappedBy="account", orphanRemoval=true)
     * @var $codes ArrayCollection
     */
    private $codes;

    /**
     * @OneToMany(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication", mappedBy="teamspeakAccount")
     */
    private $authentications;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
        $this->authentications = new ArrayCollection();
    }

    /**
     * @return ArrayCollection the list of authentications made against this account
     */
    public function getAuthentications()
    {
        return $this->authentications;
    }

    /**
     * @return String the Teamspeak UUID of the account
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * @param $uuid String the Teamspeak UUID of the account
     * @return TeamspeakAccount
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return String the name of the user when verified
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name String the name of the user when verified
     * @return TeamspeakAccount
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection a list of associated teamspeak codes (should only ever be one)
     */
    public function getCodes()
    {
        return $this->codes;
    }
}

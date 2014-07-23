<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * MinecraftAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccountRepository")
 */
class MinecraftAccount extends Account
{
    /**
     * @OneToMany(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftCode", mappedBy="account", orphanRemoval=true)
     * @var $codes ArrayCollection
     */
    private $codes;

    /**
     * @OneToMany(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\Authentication", mappedBy="minecraftAccount")
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
    public function getAuthentications()
    {
        return $this->authentications;
    }

    /**
     * @return ArrayCollection list of MinecraftCodes for this account (should only ever be one)
     */
    public function getCodes()
    {
        return $this->codes;
    }
}

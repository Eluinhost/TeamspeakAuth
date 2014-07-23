<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * TeamspeakAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccountRepository")
 */
class TeamspeakAccount extends Account
{
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
     * @return ArrayCollection a list of associated teamspeak codes (should only ever be one)
     */
    public function getCodes()
    {
        return $this->codes;
    }
}

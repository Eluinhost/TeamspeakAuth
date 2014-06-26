<?php

namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Authentication
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\AuthenticationRepository")
 */
class Authentication extends TimestampedEntity
{
    /**
     * @ManyToOne(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\TeamspeakAccount", inversedBy="authentications")
     */
    private $teamspeakAccount;

    /**
     * @ManyToOne(targetEntity="PublicUHC\Bundle\TeamspeakAuthBundle\Entity\MinecraftAccount", inversedBy="authentications")
     */
    private $minecraftAccount;

    /**
     * @return TeamspeakAccount the teamspeak account associated with this authentication
     */
    public function getTeamspeakAccount()
    {
        return $this->teamspeakAccount;
    }

    /**
     * @param TeamspeakAccount $account the teamspeak account to associate with this authentication
     * @return Authentication
     */
    public function setTeamspeakAccount(TeamspeakAccount $account)
    {
        $this->teamspeakAccount = $account;
        return $this;
    }

    /**
     * @return MinecraftAccount the Minecraft account associated with this authentication
     */
    public function getMinecraftAccount()
    {
        return $this->minecraftAccount;
    }

    /**
     * @param MinecraftAccount $account the Minecraft account to associate with this authentication
     * @return $this
     */
    public function setMinecraftAccount(MinecraftAccount $account)
    {
        $this->minecraftAccount = $account;
        return $this;
    }
}

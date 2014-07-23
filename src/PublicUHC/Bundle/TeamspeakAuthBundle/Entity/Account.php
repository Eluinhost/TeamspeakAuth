<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;

/**
 * @MappedSuperclass
 */
class Account extends TimestampedEntity {

    /**
     * @Column(type="string", length=32)
     */
    private $uuid;

    /**
     * @Column(type="string", length=32)
     */
    private $name;

    /**
     * @return String the UUID of the Minecraft account without the -
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * @param $uuid String the UUID of the Minecraft account without the -
     * @return Account
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return String the username of the account when it was verified
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name String the username of the account when it was verified
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
} 
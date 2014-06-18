<?php

namespace PublicUHC\TeamspeakAuth\Entities;


use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity
 * @Table
 */
class MinecraftCode extends DatabaseObject {

    /**
     * @Column(type="string", length=10)
     */
    private $code;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\MinecraftAccount", inversedBy="codes")
     */
    private $account;

    public function __construct() {
        $this->code = substr(md5(rand()), 0, 10);
    }

    /**
     * @return string the code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param $code string the code, should be 10 chars long at max. One is automatically generated on object construction
     * @return MinecraftCode
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return MinecraftAccount the associated Minecraft account
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * @param MinecraftAccount $account the associated Minecraft account
     * @return MinecraftCode
     */
    public function setAccount(MinecraftAccount $account) {
        $this->account = $account;
        return $this;
    }
} 
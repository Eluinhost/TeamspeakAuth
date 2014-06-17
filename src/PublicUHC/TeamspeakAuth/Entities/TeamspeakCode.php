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
class TeamspeakCode extends DatabaseObject {

    /**
     * @Column(type="string", length=10)
     */
    private $code;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount", inversedBy="codes")
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
     * @param $code String the code, one is automatically generated on object construct. 10 chars
     * @return TeamspeakCode
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @return TeamspeakAccount the associated teamspeak account
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * @param TeamspeakAccount $account the associated teamspeak account
     * @return TeamspeakCode
     */
    public function setAccount(TeamspeakAccount $account) {
        $this->account = $account;
        return $this;
    }
} 
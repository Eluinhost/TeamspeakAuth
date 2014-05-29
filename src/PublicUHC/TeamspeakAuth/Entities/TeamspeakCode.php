<?php

namespace PublicUHC\TeamspeakAuth\Entities;


use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @Entity
 * @Table(name="teamspeakcodes")
 */
class TeamspeakCode {

    use DatabaseTrait;

    /**
     * @Column(type="string")
     */
    private $code;

    /**
     * @ManyToOne(targetEntity="PublicUHC\TeamspeakAuth\Entities\TeamspeakAccount", inversedBy="codes")
     * @JoinColumn(name="TeamspeakAccountId")
     */
    private $account;

    public function __construct() {
        $this->code = substr(md5(rand()), 0, 10);
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getAccount() {
        return $this->account;
    }

    public function setAccount(TeamspeakAccount $account) {
        $this->account = $account;
        return $this;
    }
} 
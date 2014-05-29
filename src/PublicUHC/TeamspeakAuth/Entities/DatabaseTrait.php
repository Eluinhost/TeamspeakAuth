<?php
namespace PublicUHC\TeamspeakAuth\Entities;

use Doctrine\ORM\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

trait DatabaseTrait {
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     */
    private $updatedAt;

    public function getID() {
        return $this->id;
    }

    public function getCreatedAt() {
        return $this->id;
    }

    public function setCreatedAt($at) {
        $this->createdAt = $at;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($at) {
        $this->updatedAt = $at;
        return $this;
    }
} 
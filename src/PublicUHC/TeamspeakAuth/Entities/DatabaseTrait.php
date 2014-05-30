<?php
namespace PublicUHC\TeamspeakAuth\Entities;

use DateTime;
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

    /**
     * @return DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $at) {
        $this->createdAt = $at;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $at) {
        $this->updatedAt = $at;
        return $this;
    }
} 
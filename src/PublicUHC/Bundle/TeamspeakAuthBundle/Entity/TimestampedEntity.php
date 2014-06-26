<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
class TimestampedEntity {

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

    /**
     * @return int the object identifier, should not really be used directly
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return DateTime the date created at
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param DateTime $at the date created at
     * @return TimestampedEntity
     */
    public function setCreatedAt(DateTime $at) {
        $this->createdAt = $at;
        return $this;
    }

    /**
     * @return DateTime the date last updated
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $at time last updated
     * @return TimestampedEntity
     */
    public function setUpdatedAt(DateTime $at) {
        $this->updatedAt = $at;
        return $this;
    }

    /**
     * Updates the updated at time to current timestamp and sets the created at date if not set
     * @PrePersist
     * @PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
} 
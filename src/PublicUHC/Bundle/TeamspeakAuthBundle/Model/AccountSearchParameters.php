<?php
namespace PublicUHC\Bundle\TeamspeakAuthBundle\Model;

class AccountSearchParameters {

    private $uuids = [];
    private $limit = -1;
    private $offset = 0;
    private $verified = false;

    /**
     * @return array
     */
    public function getUuids()
    {
        return $this->uuids;
    }

    /**
     * Set the UUIDS to search for, [] = show all
     * @param array $uuids
     */
    public function setUuids($uuids)
    {
        $this->uuids = $uuids;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the amount to get, ignored if using UUIDs
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the offset to use, ignored if using UUIDs
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * Set whether to return verified accounts only or not
     * @param boolean $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }
} 
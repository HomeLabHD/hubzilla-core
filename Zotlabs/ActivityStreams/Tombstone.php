<?php

namespace Zotlabs\ActivityStreams;

class Tombstone extends ASObject
{
    public $formerType;
    public $deleted;

    /**
     * @return mixed
     */
    public function getFormerType()
    {
        return $this->formerType;
    }

    /**
     * @param mixed $formerType
     * @return Tombstone
     */
    public function setFormerType($formerType)
    {
        $this->formerType = $formerType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     * @return Tombstone
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }



}

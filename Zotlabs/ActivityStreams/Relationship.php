<?php

namespace Zotlabs\ActivityStreams;

class Relationship extends ASObject
{
    public $subject;
    public $object;
    public $relationship;

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     * @return Relationship
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     * @return Relationship
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelationship()
    {
        return $this->relationship;
    }

    /**
     * @param mixed $relationship
     * @return Relationship
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
        return $this;
    }



}

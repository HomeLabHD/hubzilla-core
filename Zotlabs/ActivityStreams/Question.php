<?php

namespace Zotlabs\ActivityStreams;

class Question extends ASObject
{
    public $oneOf;
    public $anyOf;
    public $closed;

    /**
     * @return mixed
     */
    public function getOneOf()
    {
        return $this->oneOf;
    }

    /**
     * @param mixed $oneOf
     * @return Question
     */
    public function setOneOf($oneOf)
    {
        $this->oneOf = $oneOf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnyOf()
    {
        return $this->anyOf;
    }

    /**
     * @param mixed $anyOf
     * @return Question
     */
    public function setAnyOf($anyOf)
    {
        $this->anyOf = $anyOf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @param mixed $closed
     * @return Question
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
        return $this;
    }



}

<?php

namespace Zotlabs\ActivityStreams;

/**
 * According to the specification, OrderedCollectionPage extends
 * both OrderedCollection and CollectionPage, but PHP is still a bit awkward
 * when it comes to multiple inheritance. Rather than try and do this with
 * traits, we'll just include the CollectionPage elements here - as this only
 * consists of three properties.
 */

class OrderedCollectionPage extends OrderedCollection
{
    public $partOf;
    public $next;
    public $prev;
    public $startIndex;

    /**
     * @return mixed
     */
    public function getPartOf()
    {
        return $this->partOf;
    }

    /**
     * @param mixed $partOf
     * @return OrderedCollectionPage
     */
    public function setPartOf($partOf)
    {
        $this->partOf = $partOf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param mixed $next
     * @return OrderedCollectionPage
     */
    public function setNext($next)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * @param mixed $prev
     * @return OrderedCollectionPage
     */
    public function setPrev($prev)
    {
        $this->prev = $prev;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @param mixed $startIndex
     * @return OrderedCollectionPage
     */
    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
        return $this;
    }

}

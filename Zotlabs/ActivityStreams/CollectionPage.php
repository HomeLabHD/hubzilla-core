<?php

namespace Zotlabs\ActivityStreams;

class CollectionPage extends Collection
{
    public $partOf;
    public $next;
    public $prev;

    // startIndex only applies for OrderedCollectionPage. See
    // https://www.w3.org/ns/activitystreams#OrderedCollectionPage
    // It is provided here to avoid multiple inheritance

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
     * @return CollectionPage
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
     * @return CollectionPage
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
     * @return CollectionPage
     */
    public function setPrev($prev)
    {
        $this->prev = $prev;
        return $this;
    }



}

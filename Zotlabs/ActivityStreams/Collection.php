<?php

namespace Zotlabs\ActivityStreams;

class Collection extends ASObject
{
    public int $totalItems;
    public string $current;
    public string $first;
    public string $last;
    public array $items;

    public mixed $collectionOf;

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param mixed $totalItems
     * @return Collection
     */
    public function setTotalItems(mixed $totalItems): static
    {
        $this->totalItems = $totalItems;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrent(): string
    {
        return $this->current;
    }

    /**
     * @param mixed $current
     * @return Collection
     */
    public function setCurrent(mixed $current): static
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirst(): string
    {
        return $this->first;
    }

    /**
     * @param mixed $first
     * @return Collection
     */
    public function setFirst(mixed $first): static
    {
        $this->first = $first;
        return $this;
    }

    /**
     * @return string
     */
    public function getLast(): string
    {
        return $this->last;
    }

    /**
     * @param mixed $last
     * @return Collection
     */
    public function setLast(mixed $last): static
    {
        $this->last = $last;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     * @return Collection
     */
    public function setItems(mixed $items): static
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCollectionOf(): mixed
    {
        return $this->collectionOf;
    }

    /**
     * @param mixed $collectionOf
     * @return Collection
     */
    public function setCollectionOf(mixed $collectionOf): static
    {
        $this->collectionOf = $collectionOf;
        return $this;
    }


}

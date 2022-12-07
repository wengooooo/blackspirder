<?php

namespace BlackSpider\Iterators;

class ExpectingIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;
    private $wasValid;

    public function __construct(\Iterator $inner)
    {
        $this->inner = $inner;
    }

    public function next(): void
    {
        if (!$this->wasValid && $this->valid()) {
            // Just do nothing, because the inner iterator has became valid.
        } else {
            $this->inner->next();
        }

        $this->wasValid = $this->valid();
    }

    public function current(): mixed
    {
        return $this->inner->current();
    }

    public function rewind(): void
    {
        $this->inner->rewind();

        $this->wasValid = $this->valid();
    }

    public function key(): mixed
    {
        return $this->inner->key();
    }

    public function valid(): bool
    {
        return $this->inner->valid();
    }
}
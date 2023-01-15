<?php
namespace BlackSpider\Iterators;
// Do not extend IteratorIterator, because it cashes the return values somehow!
use BlackSpider\Scheduling\SchedulerInterface;

class MapIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;
    private $handler;

    public function __construct(SchedulerInterface $inner, callable $handler)
    {
        $this->inner = $inner;
        $this->handler = $handler;
    }

    public function next(): void
    {
        $this->inner->next();
    }

    public function current() : mixed {
        return call_user_func($this->handler, $this->inner->current(), $this->inner);
    }

    public function rewind(): void
    {
        $this->inner->rewind();
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
<?php

namespace BlackSpider\Scheduling;

use BlackSpider\Http\Request;
use BlackSpider\Scheduling\Timing\ClockInterface;

class ArrayIteratorRequestScheduler extends \ArrayIterator
{
    private int $delay = 0;

    /**
     * @var Request[]
     */
    private \ArrayIterator $requests;

//    private array $requests = [];

    public function schedule(Request $request): void
    {
        $this->append($request);
//        $this->requests[] = $request;
    }

    public function empty(): bool
    {
//        return ($this->requests->count() <= 0);
//        return empty($this->requests);
        return $this->count() <= 0;
    }

    /**
     * @return Request[]
     */
    public function nextRequests(int $batchSize): Request
    {
        return $this->getNextRequests($batchSize);
    }

    public function forceNextRequests(int $batchSize): array
    {
        return $this->getNextRequests($batchSize);
    }

    /**
     * @return Request[]
     */
    private function getNextRequests(int $batchSize): Request
    {
        return $this->current();
    }
}
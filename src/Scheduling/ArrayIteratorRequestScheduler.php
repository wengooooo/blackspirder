<?php

namespace BlackSpider\Scheduling;

use BlackSpider\Downloader\Downloader;
use BlackSpider\Http\Request;
//class ArrayIteratorRequestScheduler extends \ArrayIterator implements SchedulerInterface
//{
//
//    public function schedule(Request $request): void
//    {
//        $this->append($request);
//    }
//
//    public function empty(): bool
//    {
//        return $this->count() <= 0;
//    }
//
//    /**
//     * @return Request
//     */
//    public function nextRequests(): Request
//    {
//        return $this->getNextRequests();
//    }
//
//    /**
//     * @return Request
//     */
//    public function getNextRequests(): Request
//    {
//        return $this->current();
//    }
//}

class ArrayIteratorRequestScheduler extends \SplQueue implements SchedulerInterface
{

    public function schedule(Request $request): void
    {
        $this->enqueue($request);
    }

    public function empty(): bool
    {
        return $this->count() <= 0;
    }

    public function valid(): bool
    {
        return $this->count() > 0;
    }

    public function current(): mixed
    {
        return $this->dequeue();
    }

    /**
     * @return Request
     */
    public function nextRequests(): Request
    {
        return $this->getNextRequests();
    }

    /**
     * @return Request
     */
    public function getNextRequests(): Request
    {
        return $this->pop();
    }
}
<?php

namespace BlackSpider\Scheduling;

use BlackSpider\Downloader\Downloader;
use BlackSpider\Http\Request;
class ArrayIteratorRequestScheduler extends \ArrayIterator
{

    public function schedule(Request $request): void
    {
        $this->append($request);
    }

    public function empty(): bool
    {
        return $this->count() <= 0;
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
    private function getNextRequests(): Request
    {
        return $this->current();
    }

}
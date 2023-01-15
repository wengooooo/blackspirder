<?php

namespace BlackSpider\Scheduling;

use BlackSpider\Http\Request;
use BlackSpider\Iterators\RedisIterator;

class RedisRequestScheduler extends RedisIterator implements SchedulerInterface
{
    public function schedule(Request $request): void
    {
        $this->client->rpush('start_urls', [serialize($request)]);
    }

    public function empty(): bool
    {
        return $this->client->llen('start_urls') <= 0;
    }

    public function nextRequests(): Request
    {
        return $this->getNextRequests();
    }

    public function getNextRequests(): Request
    {
        return $this->current();
    }
}
<?php

namespace BlackSpider\Iterators;

use Opis\Closure\Library;
use Predis\Client;

class RedisIterator implements \Iterator
{
    public Client $client;
    public function __construct()
    {
        $this->client = new Client();
        Library::init();
    }

    public function current()
    {
        return unserialize($this->client->lpop('start_urls'));
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

    public function valid():bool
    {
        return $this->client->llen('start_urls') > 0;
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}
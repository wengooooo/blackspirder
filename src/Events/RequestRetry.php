<?php

namespace BlackSpider\Events;

use BlackSpider\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use BlackSpider\Http\Request;
use Symfony\Contracts\EventDispatcher\Event;
//use GuzzleHttp\Psr7\Response;

final class RequestRetry extends Event
{
    public const NAME = 'request.retry';

    public function __construct(public Request $request, public ?Response $response, public ?GuzzleException $reason = null)
    {
    }
}
<?php

namespace BlackSpider\Scheduling;

use BlackSpider\Http\Request;

interface SchedulerInterface
{
    public function schedule(Request $request): void;
    public function empty(): bool;
    public function nextRequests(): Request;
    public function getNextRequests(): Request;
}
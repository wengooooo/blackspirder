<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 WenGo
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/wengooooo/blackspirder
 */

namespace BlackSpider\Testing\Concerns;

use Closure;
use Generator;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;

trait InteractsWithRequestsAndResponses
{
    private function makeRequest(string $url = '::url::', ?Closure $callback = null): Request
    {
        $callback ??= static function (): Generator {
            yield from [];
        };

        return new Request('GET', $url, $callback);
    }

    private function makeResponse(
        ?Request $request = null,
        int $status = 200,
        ?string $body = null,
        array $headers = [],
    ): Response {
        return new Response(
            new \GuzzleHttp\Psr7\Response($status, $headers, $body),
            $request ?: $this->makeRequest(),
        );
    }
}

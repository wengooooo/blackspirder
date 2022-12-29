# blackspirder

```php
<?php
require_once 'vendor/autoload.php';
use BlackSpider\Downloader\Middleware\RetryMiddleware;
use BlackSpider\Downloader\Middleware\UserAgentMiddleware;
use BlackSpider\Extensions\LoggerExtension;
use BlackSpider\Http\Request;
use BlackSpider\Http\Response;
use BlackSpider\Spider\BasicSpider;
use BlackSpider\Spider\Configuration\Overrides;

class MySpider extends BasicSpider
{
    public function parse(Response $response): \Generator
    {
        /***/
    }

    /** @return Request[] */
    protected function initialRequests(): array
    {
        $yesterday = (new DateTime('yesterday'))->format('Y/m/d');

        return [
            new Request(
                'GET',
                "https://www.httpbin.org/user-agent",
                [$this, 'parse']
            ),
        ];
    }
}

\BlackSpider\BlackSpider::startSpider(
    MySpider::class,
    new Overrides(
        startUrls: ['https://my-override-url.com'],
        downloaderMiddleware: [
            UserAgentMiddleware::class,
            [RetryMiddleware::class, [
                'should_retry_callback' => function (?Response $response = null): bool {
                    if (!$response) {
                        return true;
                    }

                    if(str_contains($response->getBody(), 'user')) {
                        return true;
                    }

                    return false;
                },
                ]
            ],
        ],
        extensions: [
            LoggerExtension::class
        ]
    ),
);
```
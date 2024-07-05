<?php

namespace Bangpound\Sns\HttpClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class FakeCacheHeaderResponse implements ResponseInterface
{
    public function __construct(
        private ResponseInterface $response,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(bool $throw = true): array
    {
        $headers = $this->response->getHeaders($throw);

        // One month
        $headers['cache-control'] = 'public, max-age=2592000, s-maxage=2592000';

        return $headers;
    }

    public function getContent(bool $throw = true): string
    {
        return $this->response->getContent($throw);
    }

    public function toArray(bool $throw = true): array
    {
        return $this->response->toArray($throw);
    }

    public function cancel(): void
    {
        $this->response->cancel();
    }

    public function getInfo(string $type = null): mixed
    {
        return $this->response->getInfo($type);
    }
}

<?php

namespace Bangpound\Sns\HttpClient;

use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class FakeCacheHeaderClient implements HttpClientInterface
{
    use DecoratorTrait;

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $response = $this->client->request($method, $url, $options);
        // Break Async: we don't care here, but we need all headers to be able to update them
        $response->getStatusCode();

        return new FakeCacheHeaderResponse($response) ;
    }
}

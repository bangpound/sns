<?php

namespace Bangpound\Sns\Tests\HttpClient;

use Bangpound\Sns\HttpClient\FakeCacheHeaderClient;
use Bangpound\Sns\HttpClient\FakeCacheHeaderResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(FakeCacheHeaderClient::class)]
#[UsesClass(FakeCacheHeaderResponse::class)]
class FakeCacheHeaderClientTest extends TestCase
{
    private $url = 'https://jsonplaceholder.typicode.com/posts/1';

    public function testRequestReturnsFakedCacheHeadersResponseInstance()
    {
        $response = new MockResponse();

        $httpClient = new MockHttpClient([$response]);
        $fakeCacheHeaderClient = new FakeCacheHeaderClient($httpClient);

        $response = $fakeCacheHeaderClient->request('GET', $this->url);
        $this->assertInstanceOf(FakeCacheHeaderResponse::class, $response);
    }
}

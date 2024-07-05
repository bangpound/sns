<?php

namespace Bangpound\Sns\Tests\HttpClient;

use Bangpound\Sns\HttpClient\FakeCacheHeaderClient;
use Bangpound\Sns\HttpClient\FakeCacheHeaderResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(FakeCacheHeaderResponse::class)]
#[UsesClass(FakeCacheHeaderClient::class)]
class FakeCacheHeaderResponseTest extends TestCase
{
    public function testGetHeadersContainsFakedCacheHeader()
    {
        $response = new FakeCacheHeaderResponse(new MockResponse());
        $this->assertEquals('public, max-age=2592000, s-maxage=2592000', $response->getHeaders()['cache-control']);
    }

    public function testGetStatusCode()
    {
        $response = new FakeCacheHeaderResponse(new MockResponse('', [
            'http_code' => 400,
        ]));
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetContent()
    {
        $client = new FakeCacheHeaderClient(new MockHttpClient(new MockResponse('{"foo":"bar"}')));
        $response = $client->request('GET', 'https://example.com');
        $this->assertInstanceOf(FakeCacheHeaderResponse::class, $response);
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
    }

    public function testToArray()
    {
        $client = new FakeCacheHeaderClient(new MockHttpClient(new MockResponse('{"foo":"bar"}')));
        $response = $client->request('GET', 'https://example.com');
        $this->assertInstanceOf(FakeCacheHeaderResponse::class, $response);
        $this->assertEquals(['foo' => 'bar'], $response->toArray());
    }
}

<?php

namespace Bangpound\Sns\Tests;

use Bangpound\Sns\CertificateClient;
use Bangpound\Sns\HttpClient\FakeCacheHeaderClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;

#[CoversClass(CertificateClient::class)]
class CertificateClientTest extends TestCase
{
    public function testRequestCertificateUrl()
    {
        $client = new CertificateClient(
            new CachingHttpClient(
                new FakeCacheHeaderClient(
                    HttpClient::create()
                ),
                new Store(__DIR__.'/../../var/cache/http_client_cache')
            )
        );
        $cert = $client('https://sns.us-east-2.amazonaws.com/SimpleNotificationService-01d088a6f77103d0fe307c0069e40ed6.pem');
        $this->assertIsString($cert);
    }
}

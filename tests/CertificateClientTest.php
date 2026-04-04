<?php

namespace Bangpound\Sns\Tests;

use Bangpound\Sns\CertificateClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(CertificateClient::class)]
class CertificateClientTest extends TestCase
{
    public function testRequestCertificateUrl(): void
    {
        $pemContent = "-----BEGIN CERTIFICATE-----\nfake\n-----END CERTIFICATE-----\n";
        $client = new CertificateClient(
            new MockHttpClient(new MockResponse($pemContent))
        );
        $cert = $client('https://sns.us-east-2.amazonaws.com/SimpleNotificationService-01d088a6f77103d0fe307c0069e40ed6.pem');
        $this->assertIsString($cert);
        $this->assertSame($pemContent, $cert);
    }
}

<?php

namespace Bangpound\Sns;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class CertificateClient
{
    public function __construct(#[Autowire(service: 'certificate_client.http_client.caching')]
        private HttpClientInterface $httpClient)
    {
    }

    public function __invoke(string $certUrl): string
    {
        $response = $this->httpClient->request('GET', $certUrl, [
            'verify_peer' => true,
            'verify_host' => true,
        ]);
        $response->getStatusCode();

        return $response->getContent();
    }
}

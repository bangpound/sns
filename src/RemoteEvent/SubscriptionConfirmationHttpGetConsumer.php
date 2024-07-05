<?php

namespace Bangpound\Sns\RemoteEvent;

use Override;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function assert;

class SubscriptionConfirmationHttpGetConsumer implements ConsumerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
        $this->logger = new NullLogger();
    }

    #[Override]
    public function consume(RemoteEvent $event): void
    {
        assert($event instanceof SubscriptionConfirmation);

        $this->logger->debug($event->getMessage(), ['sns' => $event->getPayload()]);
        // Confirm the subscription by sending a GET request to the SubscribeURL
        $response = $this->httpClient->request('GET', $event->getSubscribeUrl());
        $this->logger->info($response->getContent());
    }
}

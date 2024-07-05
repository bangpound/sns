<?php

namespace Bangpound\Sns\RemoteEvent;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

class UnsubscribeConfirmationConsumer implements ConsumerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    #[\Override]
    public function consume(RemoteEvent $event): void
    {
        $this->logger->notice($event->getMessage(), ['sns' => $event->getPayload()]);
    }
}

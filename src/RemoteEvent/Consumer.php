<?php

namespace Bangpound\Sns\RemoteEvent;

use Bangpound\Sns\RemoteEvent\RemoteEvent as SnsRemoteEvent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

/**
 * Class Consumer.
 *
 * This class calls one of three other object instances to handle a SNS notification.
 */
#[AsRemoteEventConsumer(name: 'sns')]
class Consumer implements ConsumerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param ContainerInterface $locator The service locator of SNS handlers. One for each SNS message type.
     */
    public function __construct(
        #[AutowireLocator('app.sns.handler', indexAttribute: 'type')]
        private readonly ContainerInterface $locator,
    ) {
        $this->logger = new NullLogger();
    }

    #[\Override]
    public function consume(RemoteEvent $event): void
    {
        \assert($event instanceof SnsRemoteEvent);

        /** @var ConsumerInterface $consumer */
        $consumer = $this->locator->get($event->getName());

        $consumer->consume($event);
    }
}

<?php

namespace Bangpound\Sns\RemoteEvent;

use Aws\Result;
use Aws\Sns\SnsClient;
use Override;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

class SubscriptionConfirmationApiConsumer implements ConsumerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly SnsClient $snsClient
    ) {
        $this->logger = new NullLogger();
    }

    #[Override]
    public function consume(RemoteEvent $event): void
    {
        if (!$event instanceof SubscriptionConfirmation) {
            throw new \InvalidArgumentException(sprintf('Expected %s, got %s.', SubscriptionConfirmation::class, $event::class));
        }

        $this->logger->debug($event->getMessage(), ['sns' => $event->getPayload()]);
        // There are two ways to confirm the subscription, and the method determines how unsubscribes
        // can work. See https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.prepare.html
        try {
            $result = $this->snsClient->confirmSubscription([
                'AuthenticateOnUnsubscribe' => 'true',
                'Token' => $event->getToken(),
                'TopicArn' => $event->getTopicArn(),
            ]);
            $this->logger->notice('Confirmed subscription {SubscriptionArn}', $result->toArray());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }
}

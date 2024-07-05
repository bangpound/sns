<?php

namespace Bangpound\Sns\RemoteEvent;

use Aws\Result;
use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Override;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

use function assert;

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
        assert($event instanceof SubscriptionConfirmation);

        $this->logger->debug($event->getMessage(), ['sns' => $event->getPayload()]);
        // There are two ways to confirm the subscription, and the method determines how unsubscribes
        // can work. See https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.prepare.html
        $promise = $this->snsClient->confirmSubscriptionAsync([
            'AuthenticateOnUnsubscribe' => 'true',
            'Token' => $event->getToken(),
            'TopicArn' => $event->getTopicArn(),
        ]);
        $promise->then(function (Result $result) {
            $this->logger->notice('Confirmed subscription {SubscriptionArn}', $result->toArray());
        }, function (SnsException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        });
        $result = $promise->wait();
    }
}

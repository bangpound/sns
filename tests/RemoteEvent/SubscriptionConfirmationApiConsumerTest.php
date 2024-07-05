<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Aws\MockHandler;
use Aws\Result;
use Aws\Sns\SnsClient;
use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\Subscribable;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmationApiConsumer;
use ColinODell\PsrTestLogger\TestLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubscriptionConfirmationApiConsumer::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
#[UsesTrait(Subscribable::class)]
class SubscriptionConfirmationApiConsumerTest extends TestCase
{
    private SubscriptionConfirmationApiConsumer $subscriptionConfirmationApiConsumer;
    private MockHandler $awsHandler;

    protected function setUp(): void
    {
        $this->awsHandler = new MockHandler();
        $snsClient = new SnsClient([
            'handler' => $this->awsHandler,
            'region' => 'test',
        ]);
        $logger = new TestLogger();
        $this->subscriptionConfirmationApiConsumer = new SubscriptionConfirmationApiConsumer($snsClient);
        $this->subscriptionConfirmationApiConsumer->setLogger($logger);
    }

    public function testConsume(): void
    {
        $this->expectNotToPerformAssertions();
        $message = new Message([
            'Message' => 'a',
            'MessageId' => 'b',
            'Timestamp' => 'c',
            'TopicArn' => 'd',
            'Type' => 'SubscriptionConfirmation',
            'Subject' => 'f',
            'Signature' => 'g',
            'SignatureVersion' => '1',
            'SigningCertURL' => 'https://sns.us-east-2.amazonaws.com/SimpleNotificationService-01d088a6f77103d0fe307c0069e40ed6.pem',
            'SubscribeURL' => 'https://www.google.com',
            'Token' => 'j',
        ]);
        $remoteEvent = new SubscriptionConfirmation($message);

        $this->awsHandler->append(new Result([
        ]));

        $this->subscriptionConfirmationApiConsumer->consume($remoteEvent);
    }
}

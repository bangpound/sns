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
#[UsesClass(SubscriptionConfirmation::class)]
#[UsesTrait(Subscribable::class)]
class SubscriptionConfirmationApiConsumerTest extends TestCase
{
    private SubscriptionConfirmationApiConsumer $subscriptionConfirmationApiConsumer;
    private MockHandler $awsHandler;
    private TestLogger $logger;

    protected function setUp(): void
    {
        $this->awsHandler = new MockHandler();
        $snsClient = new SnsClient([
            'handler' => $this->awsHandler,
            'region' => 'test',
            'credentials' => ['key' => 'test', 'secret' => 'test'],
        ]);
        $this->logger = new TestLogger();
        $this->subscriptionConfirmationApiConsumer = new SubscriptionConfirmationApiConsumer($snsClient);
        $this->subscriptionConfirmationApiConsumer->setLogger($this->logger);
    }

    public function testConsumeLogsDebugMessageAndNoticeOnSuccess(): void
    {
        $message = new Message([
            'Message' => 'You have chosen to subscribe.',
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

        $this->awsHandler->append(new Result(['SubscriptionArn' => 'arn:aws:sns:us-east-1:123456789012:MyTopic:abc123']));

        $this->subscriptionConfirmationApiConsumer->consume($remoteEvent);

        $this->assertTrue($this->logger->hasDebugRecords(), 'Expected debug log of SNS message body');
        $this->assertTrue($this->logger->hasNoticeRecords(), 'Expected notice log on successful confirmation');
    }

    public function testRejectsNonSubscriptionConfirmationEvent(): void
    {
        $event = new \Symfony\Component\RemoteEvent\RemoteEvent('some-name', 'some-id', []);
        $this->expectException(\InvalidArgumentException::class);
        $this->subscriptionConfirmationApiConsumer->consume($event);
    }

    public function testConsumeLogsErrorAndPropagatesOnAwsFailure(): void
    {
        $remoteEvent = new SubscriptionConfirmation(new Message([
            'Message' => 'You have chosen to subscribe.',
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
        ]));

        $this->awsHandler->append(new \RuntimeException('Connection failed'));

        try {
            $this->subscriptionConfirmationApiConsumer->consume($remoteEvent);
            $this->fail('Expected exception to propagate');
        } catch (\RuntimeException $e) {
            $this->assertSame('Connection failed', $e->getMessage());
            $this->assertTrue($this->logger->hasErrorRecords(), 'Expected error to be logged before re-throwing');
        }
    }
}

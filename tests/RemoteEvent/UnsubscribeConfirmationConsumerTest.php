<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\UnsubscribeConfirmation;
use Bangpound\Sns\RemoteEvent\UnsubscribeConfirmationConsumer;
use ColinODell\PsrTestLogger\TestLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * This class tests the UnsubscribeConfirmationConsumer class and its consume method.
 * The consume method is responsible for consuming RemoteEvent instances.
 */
#[CoversClass(UnsubscribeConfirmationConsumer::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
class UnsubscribeConfirmationConsumerTest extends TestCase
{
    private UnsubscribeConfirmationConsumer $consumer;
    private TestLogger $logger;

    protected function setUp(): void
    {
        $this->logger = new TestLogger();
        $this->consumer = new UnsubscribeConfirmationConsumer();
        $this->consumer->setLogger($this->logger);
    }

    /**
     * Test that the consume method correctly logs the RemoteEvent message
     * and payload.
     */
    public function testConsumesMessageAndLogsContents()
    {
        $message = new Message(
            [
                'Message' => 'a',
                'MessageId' => 'b',
                'Timestamp' => 'c',
                'TopicArn' => 'd',
                'Type' => 'UnsubscribeConfirmation',
                'Subject' => 'f',
                'Signature' => 'g',
                'SignatureVersion' => '1',
                'SigningCertURL' => 'https://sns.us-east-2.amazonaws.com/SimpleNotificationService-01d088a6f77103d0fe307c0069e40ed6.pem',
                'SubscribeURL' => 'https://www.google.com',
                'Token' => 'j',
            ]
        );
        $event = new UnsubscribeConfirmation($message);

        $this->consumer->consume($event);

        $this->assertTrue($this->logger->hasNoticeRecords());
        $this->assertTrue($this->logger->hasNotice('a'));
    }
}

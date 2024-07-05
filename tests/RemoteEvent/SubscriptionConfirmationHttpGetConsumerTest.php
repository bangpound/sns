<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\Subscribable;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmationHttpGetConsumer;
use ColinODell\PsrTestLogger\TestLogger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(SubscriptionConfirmationHttpGetConsumer::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
#[UsesTrait(Subscribable::class)]
class SubscriptionConfirmationHttpGetConsumerTest extends TestCase
{
    public function testConsume(): void
    {
        $logger = new TestLogger();
        $response = new MockResponse('sample-content');
        $httpClient = new MockHttpClient([$response]);
        $consumer = new SubscriptionConfirmationHttpGetConsumer($httpClient);
        $consumer->setLogger($logger);

        $event = new SubscriptionConfirmation(new Message([
            'Type' => 'SubscriptionConfirmation',
            'Message' => 'sample-message',
            'MessageId' => '',
            'Timestamp' => '',
            'TopicArn' => '',
            'Signature' => '',
            'SigningCertURL' => '',
            'SignatureVersion' => '',
            'Token' => '',
            'SubscribeURL' => 'http://sample.com/subscribe',
        ]));

        $consumer->consume($event);

        $this->assertTrue($logger->hasInfoRecords());
        $this->assertTrue($logger->hasRecordThatMatches('/sample-message/', LogLevel::DEBUG));
    }

}

<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\SignedMessage;
use Bangpound\Sns\RemoteEvent\Subscribable;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubscriptionConfirmation::class)]
#[CoversTrait(Subscribable::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
#[CoversTrait(SignedMessage::class)]
class SubscriptionConfirmationTest extends TestCase
{
    private function makeMessage(): Message
    {
        return new Message([
            'Type' => 'SubscriptionConfirmation',
            'MessageId' => '165545c9-2a5c-472c-8df2-7ff2be2b3b1b',
            'Token' => 'test-token',
            'TopicArn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Message' => 'You have chosen to subscribe.',
            'SubscribeURL' => 'https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription',
            'Timestamp' => '2012-04-26T20:45:04.751Z',
            'SignatureVersion' => '1',
            'Signature' => 'EXAMPLE==',
            'SigningCertURL' => 'https://sns.us-west-2.amazonaws.com/SimpleNotificationService.pem',
        ]);
    }

    public function testGetSubscribeUrl(): void
    {
        $event = new SubscriptionConfirmation($this->makeMessage());
        $this->assertSame('https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription', $event->getSubscribeUrl());
    }

    public function testGetToken(): void
    {
        $event = new SubscriptionConfirmation($this->makeMessage());
        $this->assertSame('test-token', $event->getToken());
    }

    public function testGetSignatureVersion(): void
    {
        $event = new SubscriptionConfirmation($this->makeMessage());
        $this->assertSame('1', $event->getSignatureVersion());
    }

    public function testGetSignature(): void
    {
        $event = new SubscriptionConfirmation($this->makeMessage());
        $this->assertSame('EXAMPLE==', $event->getSignature());
    }

    public function testGetSigningCertUrl(): void
    {
        $event = new SubscriptionConfirmation($this->makeMessage());
        $this->assertSame('https://sns.us-west-2.amazonaws.com/SimpleNotificationService.pem', $event->getSigningCertUrl());
    }
}

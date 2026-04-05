<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\SignedMessage;
use Bangpound\Sns\RemoteEvent\Subscribable;
use Bangpound\Sns\RemoteEvent\UnsubscribeConfirmation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnsubscribeConfirmation::class)]
#[CoversTrait(Subscribable::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
#[CoversTrait(SignedMessage::class)]
class UnsubscribeConfirmationTest extends TestCase
{
    private function makeMessage(): Message
    {
        return new Message([
            'Type' => 'UnsubscribeConfirmation',
            'MessageId' => '47138184-6831-46b8-8f7c-afc488602d7d',
            'Token' => 'resubscribe-token',
            'TopicArn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Message' => 'You have chosen to deactivate subscription.',
            'SubscribeURL' => 'https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription',
            'Timestamp' => '2012-04-26T20:06:41.581Z',
            'SignatureVersion' => '1',
            'Signature' => 'EXAMPLE==',
            'SigningCertURL' => 'https://sns.us-west-2.amazonaws.com/SimpleNotificationService.pem',
        ]);
    }

    public function testGetSubscribeUrl(): void
    {
        $event = new UnsubscribeConfirmation($this->makeMessage());
        $this->assertSame('https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription', $event->getSubscribeUrl());
    }

    public function testGetToken(): void
    {
        $event = new UnsubscribeConfirmation($this->makeMessage());
        $this->assertSame('resubscribe-token', $event->getToken());
    }
}

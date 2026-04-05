<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\Notification;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\SignedMessage;
use Bangpound\Sns\RemoteEvent\Unsubscribable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Notification::class)]
#[CoversClass(RemoteEvent::class)]
#[UsesClass(Message::class)]
#[UsesTrait(SignedMessage::class)]
#[UsesTrait(Unsubscribable::class)]
class NotificationTest extends TestCase
{
    private function makeMessage(array $overrides = []): Message
    {
        return new Message(array_merge([
            'Type' => 'Notification',
            'MessageId' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'TopicArn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Subject' => 'My First Message',
            'Message' => 'Hello world!',
            'Timestamp' => '2012-05-02T00:54:06.655Z',
            'SignatureVersion' => '1',
            'Signature' => 'EXAMPLE',
            'SigningCertURL' => 'https://sns.us-west-2.amazonaws.com/SimpleNotificationService.pem',
            'UnsubscribeURL' => 'https://sns.us-west-2.amazonaws.com/?Action=Unsubscribe',
        ], $overrides));
    }

    public function testGetTopicArn(): void
    {
        $event = new Notification($this->makeMessage());
        $this->assertSame('arn:aws:sns:us-west-2:123456789012:MyTopic', $event->getTopicArn());
    }

    public function testJsonSerialize(): void
    {
        $event = new Notification($this->makeMessage());
        $data = $event->jsonSerialize();
        $this->assertIsArray($data);
        $this->assertSame('Notification', $data['Type']);
        $this->assertSame('Hello world!', $data['Message']);
    }

    public function testGetSubjectWithSubject(): void
    {
        $event = new Notification($this->makeMessage());
        $this->assertSame('My First Message', $event->getSubject());
    }

    public function testGetSubjectReturnsNullWhenAbsent(): void
    {
        $event = new Notification($this->makeMessage(['Subject' => null]));
        $this->assertNull($event->getSubject());
    }

    public function testGetMessage(): void
    {
        $event = new Notification($this->makeMessage());
        $this->assertSame('Hello world!', $event->getMessage());
    }

    public function testGetTimestamp(): void
    {
        $event = new Notification($this->makeMessage());
        $ts = $event->getTimestamp();
        $this->assertSame('2012-05-02T00:54:06+00:00', $ts->format(\DateTimeInterface::ATOM));
    }
}

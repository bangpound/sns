<?php

namespace Bangpound\Sns\Tests;

use Bangpound\Sns\Message;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(Message::class)]
class MessageTest extends TestCase
{
    public function testFromJsonStringWithValidJson(): void
    {
        $json = '{"Type" : "Notification", "MessageId": "", "Timestamp": "", "TopicArn":"", "Signature": "", "SigningCertURL":"", "SignatureVersion":"", "Message" : "This is the message"}';
        $message = Message::fromJsonString($json);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Notification', $message->toArray()['Type']);
        $this->assertEquals('This is the message', $message->toArray()['Message']);
    }

    public function testFromJsonStringWithNonArray(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid POST data.');
        $json = 'true';
        Message::fromJsonString($json);
    }

    public function testFromJsonStringWithInvalidJson(): void
    {
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        $json = 'invalidJson';
        Message::fromJsonString($json);
    }

    public function testFromSymfonyRequestWithValidContent(): void
    {
        $symfonyRequest = new Request(content: '{"Type" : "Notification", "MessageId": "", "Timestamp": "", "TopicArn":"", "Signature": "", "SigningCertURL":"", "SignatureVersion":"", "Message" : "Good Message"}');

        $message = Message::fromSymfonyRequest($symfonyRequest);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Notification', $message->toArray()['Type']);
        $this->assertEquals('Good Message', $message->toArray()['Message']);
    }

    public function testFromSymfonyRequestWithInvalidContent(): void
    {
        $symfonyRequest = new Request(content: '{invalidJson}');

        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        Message::fromSymfonyRequest($symfonyRequest);
    }
}

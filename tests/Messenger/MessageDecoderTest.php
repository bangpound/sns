<?php

namespace Bangpound\Sns\Tests\Messenger;

use Bangpound\Sns\Message;
use Bangpound\Sns\Messenger\MessageDecoder;
use Bangpound\Sns\RemoteEvent\Notification;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(MessageDecoder::class)]
#[UsesClass(Message::class)]
#[UsesClass(Notification::class)]
#[UsesClass(RemoteEvent::class)]
class MessageDecoderTest extends TestCase
{
    private ServiceLocator $serviceLocator;
    private array $map;

    protected function setUp(): void
    {
        $this->serviceLocator = new ServiceLocator([
            'test' => function () {
                return function () {
                    return new stdClass();
                };
            },
        ]);
        $this->map = [
            [
                'factory' => 'test',
                'topic_arn' => [
                    'arn:(?<partition>\w+):(?<service>\w+):(?<region>[\w\-]+):(?<account_id>\d{12}):(?<topic_name>\w+)',
                ],
                'subject' => [
                    '(?<subject>.+) Notification',
                ],
            ],
        ];
    }

    public function testReturnsMessageClassNameFromTopicArn()
    {
        $router = new MessageDecoder($this->map, $this->serviceLocator);
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $object = $router('arn:aws:sns:us-east-2:826186905853:donation_notifications', 'Arbitrary Notification', new Notification($message));
        $this->assertInstanceOf(stdClass::class, $object);
    }

    public function testHandlesNullSubject()
    {
        $router = new MessageDecoder($this->map, $this->serviceLocator);
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $object = $router('arn:aws:sns:us-east-2:826186905853:donation_notifications', null, new Notification($message));
        $this->assertInstanceOf(stdClass::class, $object);
    }

    public function testTopicArnPatternWithForwardSlash()
    {
        // A pattern containing / should not break the regex delimiter
        $router = new MessageDecoder([
            [
                'factory' => 'test',
                'topic_arn' => [
                    'arn:aws:sns:us-east-2:(?<account_id>\d{12}):(?<topic_name>[\w/\-]+)',
                ],
                'subject' => [],
            ],
        ], $this->serviceLocator);
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $object = $router('arn:aws:sns:us-east-2:826186905853:donation/notifications', null, new Notification($message));
        $this->assertInstanceOf(stdClass::class, $object);
    }
}

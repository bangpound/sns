<?php

namespace Bangpound\Sns\Tests\Messenger;

use Bangpound\Sns\Message;
use Bangpound\Sns\Messenger\MessageDecoder;
use Bangpound\Sns\RemoteEvent\Notification;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(MessageDecoder::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
class MessageDecoderTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testReturnsMessageClassNameFromTopicArn()
    {

        $serviceLocator = new ServiceLocator([
            'test' => function () {
                return function () {
                    return new stdClass();
                };
            },
        ]);
        $router = new MessageDecoder([
            [
                'factory' => 'test',
                'topic_arn' => [
                    'arn:(?<partition>\w+):(?<service>\w+):(?<region>[\w\-]+):(?<account_id>\d{12}):(?<topic_name>\w+)',
                ],
                'subject' => [
                    '(?<subject>.+) Notification',
                ],
            ],
        ], $serviceLocator);
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $object = $router('arn:aws:sns:us-east-2:826186905853:donation_notifications', 'Arbitrary Notification', new Notification($message));
        $this->assertInstanceOf(stdClass::class, $object);
    }
}

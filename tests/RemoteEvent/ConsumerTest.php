<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\Consumer;
use Bangpound\Sns\RemoteEvent\Notification;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use Bangpound\Sns\RemoteEvent\UnsubscribeConfirmation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[CoversClass(Consumer::class)]
#[UsesClass(Message::class)]
#[UsesClass(\Bangpound\Sns\RemoteEvent\RemoteEvent::class)]
class ConsumerTest extends TestCase
{
    public function testConsumesSubscriptionConfirmation()
    {
        $this->expectNotToPerformAssertions();
        $consumer = new Consumer(new ServiceLocator([
            'SubscriptionConfirmation' => function () {
                return new class () implements ConsumerInterface {
                    #[\Override]
                    public function consume(RemoteEvent $event): void
                    {
                    }
                };
            },
        ]));
        $message = include __DIR__.'/../fixtures/sns/subscription_confirmation.php';
        $consumer->consume(new SubscriptionConfirmation($message));
    }

    public function testConsumesNotification()
    {
        $this->expectNotToPerformAssertions();
        $consumer = new Consumer(new ServiceLocator([
            'Notification' => function () {
                return new class () implements ConsumerInterface {
                    #[\Override]
                    public function consume(RemoteEvent $event): void
                    {
                    }
                };
            },
        ]));
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $consumer->consume(new Notification($message));
    }

    public function testConsumesUnsubscribeConfirmation()
    {
        $this->expectNotToPerformAssertions();
        $consumer = new Consumer(new ServiceLocator([
            'UnsubscribeConfirmation' => function () {
                return new class () implements ConsumerInterface {
                    #[\Override]
                    public function consume(RemoteEvent $event): void
                    {
                    }
                };
            },
        ]));
        $message = include __DIR__.'/../fixtures/sns/unsubscribe_confirmation.php';
        $consumer->consume(new UnsubscribeConfirmation($message));
    }
}

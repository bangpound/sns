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
#[UsesClass(Notification::class)]
#[UsesClass(SubscriptionConfirmation::class)]
#[UsesClass(UnsubscribeConfirmation::class)]
class ConsumerTest extends TestCase
{
    public function testConsumesSubscriptionConfirmation()
    {
        $innerConsumer = $this->createMock(ConsumerInterface::class);
        $consumer = new Consumer(new ServiceLocator([
            'SubscriptionConfirmation' => fn () => $innerConsumer,
        ]));
        $message = include __DIR__.'/../fixtures/sns/subscription_confirmation.php';
        $event = new SubscriptionConfirmation($message);

        $innerConsumer->expects($this->once())->method('consume')->with($event);

        $consumer->consume($event);
    }

    public function testConsumesNotification()
    {
        $innerConsumer = $this->createMock(ConsumerInterface::class);
        $consumer = new Consumer(new ServiceLocator([
            'Notification' => fn () => $innerConsumer,
        ]));
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $event = new Notification($message);

        $innerConsumer->expects($this->once())->method('consume')->with($event);

        $consumer->consume($event);
    }

    public function testConsumesUnsubscribeConfirmation()
    {
        $innerConsumer = $this->createMock(ConsumerInterface::class);
        $consumer = new Consumer(new ServiceLocator([
            'UnsubscribeConfirmation' => fn () => $innerConsumer,
        ]));
        $message = include __DIR__.'/../fixtures/sns/unsubscribe_confirmation.php';
        $event = new UnsubscribeConfirmation($message);

        $innerConsumer->expects($this->once())->method('consume')->with($event);

        $consumer->consume($event);
    }

    public function testSkipsEventWithNoRegisteredHandler()
    {
        $consumer = new Consumer(new ServiceLocator([]));
        $message = include __DIR__.'/../fixtures/sns/notification.php';
        $event = new Notification($message);

        // Should not throw even though no handler is registered
        $consumer->consume($event);
        $this->addToAssertionCount(1);
    }

    public function testRejectsNonSnsRemoteEvent()
    {
        $consumer = new Consumer(new ServiceLocator([]));
        $event = new RemoteEvent('some-name', 'some-id', []);

        $this->expectException(\InvalidArgumentException::class);
        $consumer->consume($event);
    }
}

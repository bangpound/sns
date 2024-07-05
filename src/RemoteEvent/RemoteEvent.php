<?php

namespace Bangpound\Sns\RemoteEvent;

use Bangpound\Sns\Message;
use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Symfony\Component\RemoteEvent\RemoteEvent as BaseRemoteEvent;

abstract class RemoteEvent extends BaseRemoteEvent implements JsonSerializable
{
    use SignedMessage;

    public const string NOTIFICATION = 'Notification';
    public const string SUBSCRIPTION_CONFIRMATION = 'SubscriptionConfirmation';
    public const string UNSUBSCRIBE_CONFIRMATION = 'UnsubscribeConfirmation';

    public function __construct(
        public readonly Message $message
    ) {
        parent::__construct($message['Type'], $message['MessageId'], $message->toArray());
    }

    public function getTopicArn(): string
    {
        return $this->message['TopicArn'];
    }

    public function getMessage(): string
    {
        return $this->message['Message'];
    }

    public function getTimestamp(): DateTimeInterface
    {
        return new DateTimeImmutable($this->message['Timestamp']);
    }

    public function jsonSerialize(): array
    {
        return $this->message->toArray();
    }
}

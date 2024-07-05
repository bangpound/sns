<?php

namespace Bangpound\Sns\RemoteEvent;

use Bangpound\Sns\RemoteEvent\RemoteEvent as SnsRemoteEvent;
use Bangpound\Sns\Message;
use Symfony\Component\RemoteEvent\PayloadConverterInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

class PayloadConverter implements PayloadConverterInterface
{
    public function convert(array $payload): RemoteEvent
    {
        $message = new Message($payload);

        return match ($message['Type']) {
            SnsRemoteEvent::SUBSCRIPTION_CONFIRMATION => new SubscriptionConfirmation($message),
            SnsRemoteEvent::NOTIFICATION => new Notification($message),
            SnsRemoteEvent::UNSUBSCRIBE_CONFIRMATION => new UnsubscribeConfirmation($message),
        };
    }
}

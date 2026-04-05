<?php

namespace Bangpound\Sns\RemoteEvent;

trait Unsubscribable
{
    public function getUnsubscribeURL(): string
    {
        return $this->message['UnsubscribeURL'] ?? $this->message['UnsubscribeUrl'];
    }
}

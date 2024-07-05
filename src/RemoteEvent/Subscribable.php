<?php

namespace Bangpound\Sns\RemoteEvent;

trait Subscribable
{
    public function getSubscribeUrl(): string
    {
        return $this->message['SubscribeURL'] ?? $this->message['SubscribeUrl'];
    }

    public function getToken(): string
    {
        return $this->message['Token'];
    }
}

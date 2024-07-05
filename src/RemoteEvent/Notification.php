<?php

namespace Bangpound\Sns\RemoteEvent;

class Notification extends RemoteEvent
{
    use Unsubscribable;

    public function getSubject(): ?string
    {
        return $this->message['Subject'];
    }
}

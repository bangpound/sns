<?php

namespace Bangpound\Sns\RemoteEvent;

/**
 * @codeCoverageIgnore We aren't actually using this at all, so coverage is not important.
 */
trait Unsubscribable
{
    public function getUnsubscribeURL(): string
    {
        return $this->message['UnsubscribeURL'] ?? $this->message['UnsubscribeUrl'];
    }
}

<?php

namespace Bangpound\Sns\RemoteEvent;

trait SignedMessage
{
    public function getSignatureVersion(): string
    {
        return $this->message['SignatureVersion'];
    }

    public function getSignature(): string
    {
        return $this->message['Signature'];
    }

    public function getSigningCertUrl(): string
    {
        return $this->message['SigningCertURL'] ?? $this->message['SigningCertUrl'];
    }
}

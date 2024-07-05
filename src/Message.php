<?php

namespace Bangpound\Sns;

use Aws\Sns\Message as BaseMessage;
use Override;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class Message extends BaseMessage
{
    /**
     * Creates a Message object from a JSON-decodable string.
     *
     * @param string $requestBody
     * @return Message
     */
    #[Override]
    public static function fromJsonString($requestBody): static
    {
        $data = json_decode($requestBody, true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new RuntimeException('Invalid POST data.');
        }

        return new static($data);
    }

    public static function fromSymfonyRequest(Request $request): static
    {
        return static::fromJsonString($request->getContent());
    }
}

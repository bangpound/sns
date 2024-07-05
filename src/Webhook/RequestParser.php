<?php

namespace Bangpound\Sns\Webhook;

use Bangpound\Sns\RemoteEvent\PayloadConverter;
use Bangpound\Sns\RequestMatcher\SnsHeaderRequestMatcher;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Bangpound\Sns\Message;
use Aws\Sns\MessageValidator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\PayloadConverterInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

class RequestParser extends AbstractRequestParser
{
    public function __construct(
        private readonly MessageValidator $messageValidator,
        #[Autowire(service: PayloadConverter::class)]
        private readonly PayloadConverterInterface $payloadConverter
    ) {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new MethodRequestMatcher(['POST']),
            new IsJsonRequestMatcher(),
            new SnsHeaderRequestMatcher(),
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        return $this->payloadConverter->convert($request->getPayload()->all());
    }

    protected function validate(Request $request): void
    {
        parent::validate($request);

        $message = Message::fromSymfonyRequest($request);
        try {
            $this->messageValidator->validate($message);
        } catch (InvalidSnsMessageException $e) {
            throw new RejectWebhookException(406, $e->getMessage(), $e);
        }
    }
}

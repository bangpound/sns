<?php

namespace Bangpound\Sns\RequestMatcher;

use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class SnsHeaderRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request): bool
    {
        return $request->headers->has('x-amz-sns-message-type')
            && $request->headers->has('x-amz-sns-message-id')
            && $request->headers->has('x-amz-sns-topic-arn')
            && in_array($request->headers->get('x-amz-sns-message-type'), [
                RemoteEvent::SUBSCRIPTION_CONFIRMATION,
                RemoteEvent::NOTIFICATION,
                RemoteEvent::UNSUBSCRIBE_CONFIRMATION,
            ])
            && (
                $request->headers->has('x-amz-sns-subscription-arn')
                || RemoteEvent::SUBSCRIPTION_CONFIRMATION === $request->headers->get('x-amz-sns-message-type')
            );
    }
}

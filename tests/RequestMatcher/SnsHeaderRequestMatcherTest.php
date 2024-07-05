<?php

namespace Bangpound\Sns\Tests\RequestMatcher;

use Bangpound\Sns\RequestMatcher\SnsHeaderRequestMatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(SnsHeaderRequestMatcher::class)]
class SnsHeaderRequestMatcherTest extends TestCase
{
    public function testMatchesSubscriptionConfirmation()
    {
        $request = Request::create('/');
        $request->headers->set('x-amz-sns-message-type', 'SubscriptionConfirmation');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');

        $requestMatcher = new SnsHeaderRequestMatcher();
        $result = $requestMatcher->matches($request);

        $this->assertTrue($result);
    }

    public function testMatchesNotification()
    {
        $request = Request::create('/');
        $request->headers->set('x-amz-sns-message-type', 'Notification');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');
        $request->headers->set('x-amz-sns-subscription-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96');

        $requestMatcher = new SnsHeaderRequestMatcher();
        $result = $requestMatcher->matches($request);

        $this->assertTrue($result);
    }

    public function testMatchesUnsubscribeConfirmation()
    {
        $request = Request::create('/');
        $request->headers->set('x-amz-sns-message-type', 'UnsubscribeConfirmation');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');
        $request->headers->set('x-amz-sns-subscription-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96');

        $requestMatcher = new SnsHeaderRequestMatcher();
        $result = $requestMatcher->matches($request);

        $this->assertTrue($result);
    }
}

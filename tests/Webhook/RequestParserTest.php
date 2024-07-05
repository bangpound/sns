<?php

namespace Bangpound\Sns\Tests\Webhook;

use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\MessageValidator;
use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\Notification;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use Bangpound\Sns\RemoteEvent\UnsubscribeConfirmation;
use Bangpound\Sns\RequestMatcher\SnsHeaderRequestMatcher;
use Bangpound\Sns\Webhook\RequestParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RemoteEvent\PayloadConverterInterface;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

#[CoversClass(RequestParser::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
#[UsesClass(SnsHeaderRequestMatcher::class)]
#[Small]
class RequestParserTest extends TestCase
{
    public function testParsesSubscriptionConfirmation()
    {
        $payloadJson = /* @lang json */
            <<<'EOF'
{
  "Type": "SubscriptionConfirmation",
  "MessageId": "165545c9-2a5c-472c-8df2-7ff2be2b3b1b",
  "Token": "2336412f37...",
  "TopicArn": "arn:aws:sns:us-west-2:123456789012:MyTopic",
  "Message": "You have chosen to subscribe to the topic arn:aws:sns:us-west-2:123456789012:MyTopic.\nTo confirm the subscription, visit the SubscribeURL included in this message.",
  "SubscribeURL": "https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-west-2:123456789012:MyTopic&Token=2336412f37...",
  "Timestamp": "2012-04-26T20:45:04.751Z",
  "SignatureVersion": "1",
  "Signature": "EXAMPLEpH+DcEwjAPg8O9mY8dReBSwksfg2S7WKQcikcNKWLQjwu6A4VbeS0QHVCkhRS7fUQvi2egU3N858fiTDN6bkkOxYDVrY0Ad8L10Hs3zH81mtnPk5uvvolIC1CXGu43obcgFxeL3khZl8IKvO61GWB6jI9b5+gLPoBc1Q=",
  "SigningCertURL": "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem"
}
EOF;
        $message = \Bangpound\Sns\Message::fromJsonString($payloadJson);

        $validator = $this->getMockBuilder(MessageValidator::class)->getMock();
        $validator->expects($this->once())->method('validate')->willReturn(true);

        $payloadConverter = $this->getMockBuilder(PayloadConverterInterface::class)->getMock();
        $payloadConverter->expects($this->once())->method('convert')->willReturn(new SubscriptionConfirmation($message));

        $parser = new RequestParser($validator, $payloadConverter);

        $request = Request::create('/', 'POST', [], [], [], [], $payloadJson);
        $request->headers->set('x-amz-sns-message-type', 'SubscriptionConfirmation');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');

        $event = $parser->parse($request, '');
        $this->assertInstanceOf(SubscriptionConfirmation::class, $event);
    }

    public function testParsesNotification()
    {
        $payloadJson = /* @lang json */
            <<<'EOF'
{
  "Type": "Notification",
  "MessageId": "22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324",
  "TopicArn": "arn:aws:sns:us-west-2:123456789012:MyTopic",
  "Subject": "My First Message",
  "Message": "Hello world!",
  "Timestamp": "2012-05-02T00:54:06.655Z",
  "SignatureVersion": "1",
  "Signature": "EXAMPLEw6JRN...",
  "SigningCertURL": "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem",
  "UnsubscribeURL": "https://sns.us-west-2.amazonaws.com/?Action=Unsubscribe&SubscriptionArn=arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96"
}
EOF;
        $message = \Bangpound\Sns\Message::fromJsonString($payloadJson);

        $validator = $this->getMockBuilder(MessageValidator::class)->getMock();
        $validator->expects($this->once())->method('validate');

        $payloadConverter = $this->getMockBuilder(PayloadConverterInterface::class)->getMock();
        $payloadConverter->expects($this->once())->method('convert')->willReturn(new Notification($message));

        $parser = new RequestParser($validator, $payloadConverter);

        $request = Request::create('/', 'POST', [], [], [], [], $payloadJson);
        $request->headers->set('x-amz-sns-message-type', 'Notification');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');
        $request->headers->set('x-amz-sns-subscription-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96');

        $event = $parser->parse($request, '');
        $this->assertInstanceOf(Notification::class, $event);
    }

    public function testParsesUnsubscribeConfirmation()
    {
        $payloadJson = /* @lang json */
            <<<'EOF'
{
  "Type": "UnsubscribeConfirmation",
  "MessageId": "47138184-6831-46b8-8f7c-afc488602d7d",
  "Token": "2336412f37...",
  "TopicArn": "arn:aws:sns:us-west-2:123456789012:MyTopic",
  "Message": "You have chosen to deactivate subscription arn:aws:sns:us-west-2:123456789012:MyTopic:2bcfbf39-05c3-41de-beaa-fcfcc21c8f55.\nTo cancel this operation and restore the subscription, visit the SubscribeURL included in this message.",
  "SubscribeURL": "https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-west-2:123456789012:MyTopic&Token=2336412f37fb6...",
  "Timestamp": "2012-04-26T20:06:41.581Z",
  "SignatureVersion": "1",
  "Signature": "EXAMPLEHXgJm...",
  "SigningCertURL": "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem"
}
EOF;
        $message = \Bangpound\Sns\Message::fromJsonString($payloadJson);

        $validator = $this->getMockBuilder(MessageValidator::class)->getMock();
        $validator->expects($this->once())->method('validate');

        $payloadConverter = $this->getMockBuilder(PayloadConverterInterface::class)->getMock();
        $payloadConverter->expects($this->once())->method('convert')->willReturn(new UnsubscribeConfirmation($message));

        $parser = new RequestParser($validator, $payloadConverter);

        $request = Request::create('/', 'POST', [], [], [], [], $payloadJson);
        $request->headers->set('x-amz-sns-message-type', 'UnsubscribeConfirmation');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');
        $request->headers->set('x-amz-sns-subscription-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96');

        $event = $parser->parse($request, '');
        $this->assertInstanceOf(UnsubscribeConfirmation::class, $event);
    }

    public function testRejectsMessagesWithIncorrectSignature()
    {
        $payloadJson = /* @lang json */
            <<<'EOF'
{
  "Type": "SubscriptionConfirmation",
  "MessageId": "165545c9-2a5c-472c-8df2-7ff2be2b3b1b",
  "Token": "2336412f37...",
  "TopicArn": "arn:aws:sns:us-west-2:123456789012:MyTopic",
  "Message": "You have chosen to subscribe to the topic arn:aws:sns:us-west-2:123456789012:MyTopic.\nTo confirm the subscription, visit the SubscribeURL included in this message.",
  "SubscribeURL": "https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-west-2:123456789012:MyTopic&Token=2336412f37...",
  "Timestamp": "2012-04-26T20:45:04.751Z",
  "SignatureVersion": "1",
  "Signature": "EXAMPLEpH+DcEwjAPg8O9mY8dReBSwksfg2S7WKQcikcNKWLQjwu6A4VbeS0QHVCkhRS7fUQvi2egU3N858fiTDN6bkkOxYDVrY0Ad8L10Hs3zH81mtnPk5uvvolIC1CXGu43obcgFxeL3khZl8IKvO61GWB6jI9b5+gLPoBc1Q=",
  "SigningCertURL": "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem"
}
EOF;
        $payload = json_decode($payloadJson, true);

        $validator = $this->getMockBuilder(MessageValidator::class)->getMock();
        $validator->expects($this->once())->method('validate')->willThrowException(new InvalidSnsMessageException('The message signature is invalid.'));

        $payloadConverter = $this->getMockBuilder(PayloadConverterInterface::class)->getMock();
        $payloadConverter->expects($this->never())->method('convert');

        $parser = new RequestParser($validator, $payloadConverter);

        $request = Request::create('/', 'POST', [], [], [], [], $payloadJson);
        $request->headers->set('x-amz-sns-message-type', 'SubscriptionConfirmation');
        $request->headers->set('x-amz-sns-message-id', '165545c9-2a5c-472c-8df2-7ff2be2b3b1b');
        $request->headers->set('x-amz-sns-topic-arn', 'arn:aws:sns:us-west-2:123456789012:MyTopic');

        $this->expectException(RejectWebhookException::class);
        $this->expectExceptionMessage('The message signature is invalid.');
        $parser->parse($request, '');
    }
}

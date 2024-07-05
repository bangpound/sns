<?php

namespace Bangpound\Sns\Tests\RemoteEvent;

use Bangpound\Sns\Message;
use Bangpound\Sns\RemoteEvent\PayloadConverter;
use Bangpound\Sns\RemoteEvent\RemoteEvent;
use Bangpound\Sns\RemoteEvent\SubscriptionConfirmation;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PayloadConverter::class)]
#[UsesClass(Message::class)]
#[UsesClass(RemoteEvent::class)]
class PayloadConverterTest extends TestCase
{
    public function testConvertsPayload()
    {
        $payloadConverter = new PayloadConverter();
        $event = $payloadConverter->convert([
            'Type' => 'SubscriptionConfirmation',
            'MessageId' => '165545c9-2a5c-472c-8df2-7ff2be2b3b1b',
            'SubscribeURL' => 'https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-west-2:123456789012:MyTopic&Token=2336412f37...',
            'Token' => '2336412f37...',
            'TopicArn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Message' => 'Hello world!',
            'Timestamp' => '2012-05-02T00:54:06.655Z',
            'SignatureVersion' => '1',
            'Signature' => 'EXAMPLEpH+DcEwjAPg8O9mY8dReBSwksfg2S7WKQcikcNKWLQjwu6A4VbeS0QHVCkhRS7fUQvi2egU3N858fiTDN6bkkOxYDVrY0Ad8L10Hs3zH81mtnPk5uvvolIC1CXGu43obcgFxeL3khZl8IKvO61GWB6jI9b5+gLPoBc1Q=',
            'SigningCertURL' => 'https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem',
        ]);
        $this->assertInstanceOf(SubscriptionConfirmation::class, $event);
        $this->assertInstanceOf(DateTimeInterface::class, $event->getTimestamp());
    }
}

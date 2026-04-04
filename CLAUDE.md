# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run all tests
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/Webhook/RequestParserTest.php

# Run a single test method
vendor/bin/phpunit --filter testMethodName tests/Webhook/RequestParserTest.php

# Install dependencies
composer install

# Fix code style
vendor/bin/php-cs-fixer fix
```

PHPUnit is configured strictly: `requireCoverageMetadata=true`, `failOnRisky=true`, `failOnWarning=true`. New test classes need coverage attributes.

## Architecture

This is a PHP library integrating AWS SNS webhooks with Symfony's Webhook and RemoteEvent components. The flow:

1. **Incoming HTTP request** → `SnsHeaderRequestMatcher` validates required `x-amz-sns-*` headers
2. **`Webhook/RequestParser`** parses the request body into a `Message`, validates the AWS signature via `CertificateClient`, then delegates to `RemoteEvent/PayloadConverter`
3. **`PayloadConverter`** maps SNS `Type` field to one of three `RemoteEvent` subclasses: `Notification`, `SubscriptionConfirmation`, or `UnsubscribeConfirmation`
4. **`RemoteEvent/Consumer`** routes events to registered handlers via a PSR-11 service locator keyed by event name
5. **`Messenger/MessageDecoder`** (optional) routes `Notification` events to Symfony Messenger message factories, matched by topic ARN and subject regex patterns

### Key design points

- `Message` extends the AWS SDK `BaseMessage` and adds `fromJson()` and `fromRequest()` factory methods
- `CertificateClient` wraps a Symfony HTTP client; `FakeCacheHeaderClient`/`FakeCacheHeaderResponse` inject cache headers on certificate responses (AWS SNS certificates don't always return proper cache headers)
- Traits (`SignedMessage`, `Subscribable`, `Unsubscribable`) compose shared fields onto the three event types
- The `SubscriptionConfirmation` event has two consumer implementations: `SubscriptionConfirmationApiConsumer` (calls the subscribe URL via HTTP) and one that handles HTTP GET confirmation

### Test fixtures

`tests/fixtures/sns/` contains PHP files returning arrays with sample SNS payloads for all three message types. Tests use these fixtures directly rather than mocking the AWS validator.

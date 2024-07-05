<?php

namespace Bangpound\Sns\Messenger;

use Bangpound\Sns\RemoteEvent\Notification;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

class MessageDecoder implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param array{topic_arn: string[], subject: string[], type: string}[] $map
     */
    public function __construct(
        #[Autowire(param: 'sns_router')]
        private readonly array $map,
        #[AutowireLocator('app.sns.message_factory')]
        private readonly ContainerInterface $locator
    ) {
        $this->logger = new NullLogger();
    }

    /**
     * @throws PcreException
     */
    public function __invoke(string $topicArn, ?string $subject, Notification $notification): object
    {
        $matches = $this->matches($topicArn, $subject);
        $this->logger->info('SNS message on topic {topic_arn} with subject {subject} will use {factory}', [
            'topic_arn' => $topicArn,
            'subject' => $subject,
            ...$matches,
        ]);
        \assert(1 === count($matches));
        $match = array_pop($matches);

        return $this->locator->get($match['factory'])($notification, ...$match['topic_arn'], ...$match['subject']);
    }

    /**
     * @throws PcreException
     */
    private function matches(string $topicArn, ?string $subject): array
    {
        $matches = [];
        foreach ($this->map as $mapping) {
            $match = $this->buildMatchFromMapping($mapping, $topicArn, $subject);
            if (!empty($match['factory'])) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * @throws PcreException
     */
    private function buildMatchFromMapping(array $mapping, string $topicArn, ?string $subject): array
    {
        $match = [
            'arguments' => [],
        ];
        foreach ($mapping['topic_arn'] as $mappingTopicArn) {
            $this->applyPatternAndBuildMatch($match, $mappingTopicArn, $topicArn, $mapping['factory'], 'topic_arn');
        }
        if (1 === count($mapping['topic_arn'])) {
            $match['arguments'] = $match['topic_arn'][0];
        }

        if ($subject) {
            foreach ($mapping['subject'] as $mappingSubject) {
                $this->applyPatternAndBuildMatch($match, $mappingSubject, $subject, $mapping['factory'], 'subject');
            }
            if (1 === count($mapping['subject'])) {
                $match['arguments'] = array_merge($match['arguments'], $match['subject'][0]);
            }
        }

        return $match;
    }

    /**
     * @throws PcreException
     */
    private function applyPatternAndBuildMatch(array &$match, string $mappingItem, string $keyItem, string $mappingFactory, string $keyName): void
    {
        $m = [];
        $matchCount = preg_match(sprintf('/%s/', $mappingItem), $keyItem, $m);
        if ($matchCount > 0) {
            $match['factory'] = $mappingFactory;
            $m = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            $match[$keyName][] = $m;
        }
    }
}

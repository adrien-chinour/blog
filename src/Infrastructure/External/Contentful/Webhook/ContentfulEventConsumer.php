<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Webhook;

use App\Application\Event\Article\ArticlePublishedEvent;
use App\Application\Event\Article\ArticleUnpublishedEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('contentful')]
final class ContentfulEventConsumer implements ConsumerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly MessageBusInterface $eventBus
    ) {}

    public function consume(RemoteEvent $event): void
    {
        if (!($event instanceof ContentfulRemoteEvent)) {
            $this->logger?->error(sprintf('Event must be of type %s.', ContentfulRemoteEvent::class));
            return;
        }

        $payload = $event->getPayload();
        match ($type = $payload['sys']['contentType']['sys']['id'] ?? null) {
            'blogPage' => $this->handleBlogPageEvent($event),
            default => $this->logger?->warning('No consumer on type {type}', ['type' => $type]),
        };
    }

    private function handleBlogPageEvent(ContentfulRemoteEvent $event): void
    {
        $payload = $event->getPayload();
        if (null === ($id = $payload['sys']['id'] ?? null)) {
            $this->logger?->error('Fail to resolve resource id from payload', [
                'payload' => $payload,
                'event' => $event,
            ]);

            return;
        }

        $message = match ($event->getTopic()) {
            'ContentManagement.Entry.publish' => new ArticlePublishedEvent($id),
            'ContentManagement.Entry.unpublish' => new ArticleUnpublishedEvent($id),
            default => null,
        };

        if (null === $message) {
            $this->logger?->warning('No event linked for topic {topicName}', [
                'topicName' => $event->getTopic(),
            ]);

            return;
        }

        $this->eventBus->dispatch($message);
    }
}

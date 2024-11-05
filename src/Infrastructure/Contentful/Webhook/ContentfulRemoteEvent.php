<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Webhook;

use Symfony\Component\RemoteEvent\RemoteEvent;

final class ContentfulRemoteEvent extends RemoteEvent
{
    private string $topic;

    public function __construct(string $id, string $topic, array $payload)
    {
        parent::__construct('contentful', $id, $payload);
        $this->topic = $topic;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }
}

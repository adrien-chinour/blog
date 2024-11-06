<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\TagCacheInvalidation;

use App\Application\Command\TagCacheInvalidation\TagCacheInvalidationCommand;
use App\Application\Command\TagCacheInvalidation\TagCacheInvalidationCommandHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class TagCacheInvalidationCommandHandlerTest extends TestCase
{
    private TagAwareCacheInterface&MockObject $tagAwareCache;

    private TagCacheInvalidationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->tagAwareCache = $this->createMock(TagAwareCacheInterface::class);

        $this->handler = new TagCacheInvalidationCommandHandler(
            $this->tagAwareCache
        );
    }

    public function testCacheInvalidationCallInvalidateTag(): void
    {
        $this->tagAwareCache
            ->expects($this->once())
            ->method('invalidateTags')
            ->with(['article']);

        ($this->handler)(new TagCacheInvalidationCommand(['article']));
    }
}

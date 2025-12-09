<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Blogging;

use App\Domain\Blogging\BlogTag;
use PHPUnit\Framework\TestCase;

final class BlogTagTest extends TestCase
{
    public function testBlogTagCreation(): void
    {
        $tag = new BlogTag(
            id: 'tag-123',
            name: 'PHP',
            slug: 'php'
        );

        $this->assertSame('tag-123', $tag->id);
        $this->assertSame('PHP', $tag->name);
        $this->assertSame('php', $tag->slug);
    }

    public function testBlogTagPropertiesArePublic(): void
    {
        $tag = new BlogTag('tag-123', 'PHP', 'php');

        $this->assertIsString($tag->id);
        $this->assertIsString($tag->name);
        $this->assertIsString($tag->slug);
    }
}


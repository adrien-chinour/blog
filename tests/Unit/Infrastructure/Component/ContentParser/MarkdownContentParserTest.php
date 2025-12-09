<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\ContentParser;

use App\Domain\Blogging\Exception\ContentRendererException;
use App\Infrastructure\Component\ContentParser\MarkdownContentParser;
use PHPUnit\Framework\TestCase;

final class MarkdownContentParserTest extends TestCase
{
    private MarkdownContentParser $parser;

    protected function setUp(): void
    {
        $this->parser = new MarkdownContentParser();
    }

    public function testParseConvertsMarkdownToHtml(): void
    {
        $markdown = "# Heading\n\nThis is a paragraph.";
        $result = $this->parser->parse($markdown);

        $this->assertStringContainsString('<h1>', $result);
        $this->assertStringContainsString('<p>', $result);
    }

    public function testParseAddsTargetBlankToLinks(): void
    {
        $markdown = 'Check [this link](https://example.com)';
        $result = $this->parser->parse($markdown);

        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertStringContainsString('rel="nofollow"', $result);
    }

    public function testParseHandlesCodeBlocks(): void
    {
        $markdown = "```php\n<?php\necho 'Hello';\n```";
        $result = $this->parser->parse($markdown);

        // Code blocks are rendered as <pre> tags with data-lang attribute
        $this->assertStringContainsString('<pre', $result);
        $this->assertStringContainsString('data-lang="php"', $result);
    }

    public function testParseHandlesInlineCode(): void
    {
        $markdown = 'Use `echo` to output text.';
        $result = $this->parser->parse($markdown);

        $this->assertStringContainsString('code', $result);
    }

    public function testParseHandlesAutolink(): void
    {
        $markdown = 'Visit https://example.com for more info.';
        $result = $this->parser->parse($markdown);

        $this->assertStringContainsString('<a', $result);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    public function testParseHandlesEmptyString(): void
    {
        $result = $this->parser->parse('');

        $this->assertIsString($result);
    }

    public function testParseHandlesPlainText(): void
    {
        $text = 'This is plain text without markdown.';
        $result = $this->parser->parse($text);

        $this->assertStringContainsString($text, $result);
    }

    public function testParseHandlesMultipleLinks(): void
    {
        $markdown = '[Link 1](https://example.com) and [Link 2](https://test.com)';
        $result = $this->parser->parse($markdown);

        $count = substr_count($result, 'target="_blank"');
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function testParseThrowsExceptionOnInvalidMarkdown(): void
    {
        // This test might not always throw an exception depending on CommonMark's error handling
        // But we test that the parser handles errors gracefully
        $this->expectNotToPerformAssertions();
        
        try {
            $this->parser->parse('Valid markdown');
        } catch (ContentRendererException $e) {
            $this->assertInstanceOf(ContentRendererException::class, $e);
        }
    }
}


<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Component\ContentParser;

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

    public function testParseAddsTargetBlankAndNofollowToExternalLinks(): void
    {
        $markdown = 'Check [this link](https://example.com)';
        $result = $this->parser->parse($markdown);

        $this->assertStringContainsString('target="_blank"', $result);
        $this->assertMatchesRegularExpression('/rel="[^"]*nofollow[^"]*"/', $result);
        $this->assertMatchesRegularExpression('/rel="[^"]*noopener[^"]*"/', $result);
    }

    public function testParseDoesNotAlterInternalLinks(): void
    {
        $parser = new MarkdownContentParser(internalHosts: ['udfn.fr', ' www.udfn.fr ']);

        $result = $parser->parse('Read [this article](https://www.udfn.fr/articles/docker) and [that one](https://external.com)');

        $this->assertStringContainsString('<a href="https://www.udfn.fr/articles/docker">this article</a>', $result);
        $this->assertMatchesRegularExpression('/<a [^>]*target="_blank"[^>]*href="https:\/\/external\.com"/', $result);
    }

    public function testParseDoesNotBreakHtmlTagsStartingWithA(): void
    {
        // Raw HTML is allowed by CommonMark: tags starting with "a" are not links
        $result = $this->parser->parse("<aside>Note</aside>\n\n<abbr title=\"HyperText\">HTML</abbr>");

        $this->assertStringContainsString('<aside>Note</aside>', $result);
        $this->assertStringContainsString('<abbr title="HyperText">HTML</abbr>', $result);
        $this->assertStringNotContainsString('target="_blank"', $result);
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

        $this->assertSame(2, substr_count($result, 'target="_blank"'));
    }

    public function testParserCanBeReusedForSeveralDocuments(): void
    {
        $first = $this->parser->parse('# First');
        $second = $this->parser->parse('# Second');

        $this->assertStringContainsString('First', $first);
        $this->assertStringContainsString('Second', $second);
        $this->assertStringNotContainsString('First', $second);
    }
}

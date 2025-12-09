<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Social;

use App\Domain\Social\CommentSanitizer;
use PHPUnit\Framework\TestCase;

final class CommentSanitizerTest extends TestCase
{
    private CommentSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new CommentSanitizer();
    }

    // Username Sanitization Tests

    public function testSanitizeUsernameTrimsWhitespace(): void
    {
        $result = $this->sanitizer->sanitizeUsername('  username  ');
        $this->assertSame('username', $result);
    }

    public function testSanitizeUsernameCollapsesMultipleSpaces(): void
    {
        $result = $this->sanitizer->sanitizeUsername('user    name');
        $this->assertSame('user name', $result);
    }

    public function testSanitizeUsernameHandlesTabs(): void
    {
        $result = $this->sanitizer->sanitizeUsername("user\t\tname");
        $this->assertSame('user name', $result);
    }

    public function testSanitizeUsernameHandlesMixedWhitespace(): void
    {
        $result = $this->sanitizer->sanitizeUsername("  user \t  name  ");
        $this->assertSame('user name', $result);
    }

    public function testSanitizeUsernamePreservesValidCharacters(): void
    {
        $result = $this->sanitizer->sanitizeUsername('user_name-123');
        $this->assertSame('user_name-123', $result);
    }

    // Message Sanitization Tests

    public function testSanitizeMessageTrimsWhitespace(): void
    {
        $result = $this->sanitizer->sanitizeMessage('  message  ');
        $this->assertSame('message', $result);
    }

    public function testSanitizeMessageNormalizesLineBreaks(): void
    {
        $result = $this->sanitizer->sanitizeMessage("line1\r\nline2\rline3");
        $this->assertSame("line1\nline2\nline3", $result);
    }

    public function testSanitizeMessageRemovesControlCharacters(): void
    {
        $message = "Hello\x00World\x01Test";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame('HelloWorldTest', $result);
    }

    public function testSanitizeMessagePreservesNewlines(): void
    {
        $message = "line1\nline2\nline3";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame("line1\nline2\nline3", $result);
    }

    public function testSanitizeMessageConvertsTabsToSpaces(): void
    {
        $message = "line1\tline2";
        $result = $this->sanitizer->sanitizeMessage($message);
        // Tabs are converted to spaces in the sanitization process
        $this->assertStringNotContainsString("\t", $result);
        $this->assertStringContainsString(' ', $result);
    }

    public function testSanitizeMessageLimitsConsecutiveNewlines(): void
    {
        $message = "line1\n\n\n\nline2";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame("line1\n\nline2", $result);
    }

    public function testSanitizeMessageNormalizesSpaces(): void
    {
        $message = "word1    word2     word3";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame('word1 word2 word3', $result);
    }

    public function testSanitizeMessageNormalizesTabs(): void
    {
        $message = "word1\t\t\tword2";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame('word1 word2', $result);
    }

    public function testSanitizeMessageHandlesComplexWhitespace(): void
    {
        $message = "  word1   \t  word2  \n\n\n  word3  ";
        $result = $this->sanitizer->sanitizeMessage($message);
        // Tabs are converted to spaces, multiple spaces collapsed, newlines limited to 2
        // Spaces around newlines are preserved (normalized to single space)
        $this->assertSame("word1 word2 \n\n word3", $result);
    }

    public function testSanitizeMessagePreservesValidContent(): void
    {
        $message = "This is a valid message with normal content.";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame($message, $result);
    }

    public function testSanitizeMessageHandlesEmptyString(): void
    {
        $result = $this->sanitizer->sanitizeMessage('');
        $this->assertSame('', $result);
    }

    public function testSanitizeMessageHandlesOnlyWhitespace(): void
    {
        $result = $this->sanitizer->sanitizeMessage('   ');
        $this->assertSame('', $result);
    }

    public function testSanitizeMessageHandlesUnicodeCharacters(): void
    {
        $message = "Hello 世界  مرحبا";
        $result = $this->sanitizer->sanitizeMessage($message);
        $this->assertSame('Hello 世界 مرحبا', $result);
    }
}


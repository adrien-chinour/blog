<?php

declare(strict_types=1);

namespace App\Infrastructure\ContentParser;

use App\Domain\Blogging\Exception\ContentRendererException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\MarkdownConverter;
use Tempest\Highlight\CommonMark\CodeBlockRenderer;
use Tempest\Highlight\CommonMark\InlineCodeBlockRenderer;

/**
 * Convert markdown content to HTML
 */
final readonly class MarkdownContentParser implements ContentParserInterface
{
    /**
     * @throws ContentRendererException
     */
    public function parse(string $content): string
    {
        try {
            $content = $this->converter()->convert($content);
        } catch (CommonMarkException $e) {
            throw new ContentRendererException("Fail to renderer content", previous: $e);
        }

        // add on all content links target blank and nofollow
        return str_replace('<a', '<a target="_blank" rel="nofollow"', $content->getContent());
    }

    private function converter(): MarkdownConverter
    {
        $environnement = new Environment();

        $environnement
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new AutolinkExtension())
            ->addRenderer(FencedCode::class, new CodeBlockRenderer())
            ->addRenderer(Code::class, new InlineCodeBlockRenderer());

        return new MarkdownConverter($environnement);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Component\ContentParser;

use App\Domain\Blogging\Exception\ContentRendererException;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkProcessor;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tempest\Highlight\CommonMark\CodeBlockRenderer;
use Tempest\Highlight\CommonMark\InlineCodeBlockRenderer;

#[AsAlias(ContentParserInterface::class)]
final readonly class MarkdownContentParser implements ContentParserInterface
{
    private MarkdownConverter $converter;

    /**
     * @param string[] $internalHosts hosts considered as "internal": their links are not opened in a new
     *                                window and do not receive nofollow/noopener/noreferrer attributes
     */
    public function __construct(array $internalHosts = [])
    {
        $environment = new Environment([
            'external_link' => [
                'internal_hosts' => self::normalizeHosts($internalHosts),
                'open_in_new_window' => true,
                'nofollow' => ExternalLinkProcessor::APPLY_EXTERNAL,
                'noopener' => ExternalLinkProcessor::APPLY_EXTERNAL,
                'noreferrer' => ExternalLinkProcessor::APPLY_EXTERNAL,
            ],
        ]);

        $environment
            ->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new AutolinkExtension())
            ->addExtension(new ExternalLinkExtension())
            ->addRenderer(FencedCode::class, new CodeBlockRenderer())
            ->addRenderer(Code::class, new InlineCodeBlockRenderer());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @throws ContentRendererException
     */
    public function parse(string $content): string
    {
        try {
            return $this->converter->convert($content)->getContent();
        } catch (CommonMarkException $e) {
            throw new ContentRendererException('Fail to render content', previous: $e);
        }
    }

    /**
     * @return string[]
     */
    private static function normalizeHosts(array $hosts): array
    {
        $normalized = array_map(
            static fn (mixed $host) => is_string($host) ? strtolower(trim($host)) : '',
            $hosts
        );

        return array_values(array_filter($normalized, static fn (string $host) => $host !== ''));
    }
}

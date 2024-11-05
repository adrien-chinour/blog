<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Webhook;

use SensitiveParameter;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\HeaderRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class ContentfulRequestParser extends AbstractRequestParser
{
    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new MethodRequestMatcher('POST'),
            new HeaderRequestMatcher([
                'x-contentful-signature',
                'x-contentful-timestamp',
                'x-contentful-crn',
                'x-contentful-topic',
                'x-contentful-signed-headers',
            ])
        ]);
    }

    protected function doParse(Request $request, #[SensitiveParameter] string $secret): ?RemoteEvent
    {
        $timestamp = (int)$request->headers->get('x-contentful-timestamp');
        if (($timestamp / 1000) + 60 < time()) {
            throw new RejectWebhookException(406, 'The TTL duration has been exceeded.');
        }

        if (!$this->validateRequestSignature($request, $secret)) {
            throw new RejectWebhookException(406, 'Webhook signature mismatch.');
        }

        if (
            null === ($crn = $request->headers->get('x-contentful-crn'))
            || null === ($topic = $request->headers->get('x-contentful-topic'))
        ) {
            return null;
        }

        return new ContentfulRemoteEvent($crn, $topic, $request->toArray());
    }

    private function validateRequestSignature(Request $request, #[SensitiveParameter] string $secret): bool
    {
        $headers = array_map(
            fn (string $name) => sprintf('%s:%s', strtolower($name), $request->headers->get($name)),
            explode(',', $request->headers->get('x-contentful-signed-headers') ?? '')
        );

        $canonicalRequestRepresentation = [
            $request->getMethod(),
            $request->getRequestUri(),
            implode(';', $headers),
            $request->getContent(),
        ];

        $signature = hash_hmac('sha256', implode('\n', $canonicalRequestRepresentation), $secret);

        return $signature === $request->headers->get('x-contentful-signature');
    }
}

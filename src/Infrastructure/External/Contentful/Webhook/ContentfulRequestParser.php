<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Contentful\Webhook;

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
                'x-contentful-secret',
                'x-contentful-timestamp',
                'x-contentful-crn',
                'x-contentful-topic',
            ])
        ]);
    }

    protected function doParse(Request $request, #[SensitiveParameter] string $secret): ?RemoteEvent
    {
        $timestamp = (int)$request->headers->get('x-contentful-timestamp');
        if (($timestamp / 1000) + 60 < time()) {
            throw new RejectWebhookException(406, 'The TTL duration has been exceeded.');
        }

        if ($secret !== $request->headers->get('x-contentful-secret')) {
            throw new RejectWebhookException(406, 'Webhook signature mismatch.');
        }

        if (
            null === ($crn = $request->headers->get('x-contentful-crn'))
            || null === ($topic = $request->headers->get('x-contentful-topic'))
        ) {
            throw new RejectWebhookException(406, 'Missing value on required headers.');
        }

        return new ContentfulRemoteEvent($crn, $topic, $request->toArray());
    }
}

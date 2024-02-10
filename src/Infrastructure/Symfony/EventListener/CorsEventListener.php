<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(ResponseEvent::class)]
class CorsEventListener
{
    public function __invoke(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add([
            'Access-Control-Allow-Origin' => 'https://*.chinour.dev, https://*.grafana.net',
            'Content-Security-Policy' => $this->getContentSecurityPolicy(),
        ]);
    }

    private function getContentSecurityPolicy(): string
    {
        $policies = [
            'default-src' => [
                "'self'",
                'https://*.chinour.dev',
                'https://*.grafana.net',
                "'unsafe-inline'",
            ],
            'img-src' => [
                'data:',
                'https://images.ctfassets.net',
            ],
            'child-src' => [
                "'none'",
            ],
        ];

        return implode(
            ' ; ',
            array_map(
                fn (string $policy, array $rules) => sprintf('%s %s', $policy, implode(' ', $rules)),
                array_keys($policies),
                array_values($policies),
            )
        );
    }
}

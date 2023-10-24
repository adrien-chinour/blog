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
            'Access-Control-Allow-Origin' => 'https://ackee.chinour.dev'
        ]);
    }
}

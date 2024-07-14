<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->autowire(AdapterInterface::class, FilesystemAdapter::class);
    }
}

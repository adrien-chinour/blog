<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony;

use App\Infrastructure\Symfony\DependencyInjection\CompilerPass\CustomCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CustomCompilerPass());
    }
}

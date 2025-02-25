<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Baserow\Model;

/**
 * @template T of object
 */
interface BaserowModelInterface
{
    /**
     * @return T
     */
    public function toDomain();
}

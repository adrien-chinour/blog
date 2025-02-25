<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Baserow\Model;

use App\Domain\Config\Feature as DomainFeature;

/**
 * @implements BaserowModelInterface<DomainFeature>
 */
final readonly class Feature implements BaserowModelInterface
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $enable,
    ) {}

    public function toDomain(): DomainFeature
    {
        return new DomainFeature($this->name, $this->description, $this->enable);
    }
}

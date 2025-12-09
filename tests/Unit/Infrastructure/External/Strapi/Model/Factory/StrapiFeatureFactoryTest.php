<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\External\Strapi\Model\Factory;

use App\Domain\Config\Feature;
use App\Infrastructure\External\Strapi\Model\ContentType\FeatureContentType;
use App\Infrastructure\External\Strapi\Model\Factory\StrapiFeatureFactory;
use PHPUnit\Framework\TestCase;

final class StrapiFeatureFactoryTest extends TestCase
{
    private StrapiFeatureFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StrapiFeatureFactory();
    }

    public function testCreateFromModelCreatesFeature(): void
    {
        $featureContentType = $this->createFeatureContentType('new-feature', 'A new feature', true);

        $feature = $this->factory->createFromModel($featureContentType);

        $this->assertInstanceOf(Feature::class, $feature);
        $this->assertSame('new-feature', $feature->name);
        $this->assertSame('A new feature', $feature->description);
        $this->assertTrue($feature->enable);
    }

    public function testCreateFromModelHandlesNullDescription(): void
    {
        $featureContentType = $this->createFeatureContentType('feature', null, false);

        $feature = $this->factory->createFromModel($featureContentType);

        $this->assertSame('', $feature->description);
    }

    public function testCreateFromModelHandlesEmptyDescription(): void
    {
        $featureContentType = $this->createFeatureContentType('feature', '', true);

        $feature = $this->factory->createFromModel($featureContentType);

        $this->assertSame('', $feature->description);
    }

    private function createFeatureContentType(string $name, ?string $description, bool $enabled): FeatureContentType
    {
        $feature = new FeatureContentType();
        $feature->name = $name;
        $feature->description = $description;
        $feature->enabled = $enabled;

        return $feature;
    }
}


<?php

declare(strict_types=1);

namespace App\Tests\Architecture\Rules;

use Arkitect\Expression\ForClasses\NotHaveDependencyOutsideNamespace;
use Arkitect\Expression\ForClasses\NotResideInTheseNamespaces;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;

final readonly class LayerRules implements RulesInterface
{
    private const APPLICATION_LAYER = 'App\Application';
    private const DOMAIN_LAYER = 'App\Domain';
    private const PRESENTATION_LAYER = 'App\Presentation';
    private const INFRASTRUCTURE_LAYER = 'App\Infrastructure';

    public static function rules(): array
    {
        $rules = [];

        $rules[] = Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces(self::INFRASTRUCTURE_LAYER))
            ->should(new NotResideInTheseNamespaces(self::PRESENTATION_LAYER, self::DOMAIN_LAYER, self::APPLICATION_LAYER))
            ->because('infrastructure cannot be injected in other layers');

        $rules[] = Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces(self::DOMAIN_LAYER))
            ->should(new NotHaveDependencyOutsideNamespace(self::DOMAIN_LAYER, excludeCoreNamespace: true))
            ->because('domain must not depend on any other layer');

        return $rules;
    }
}

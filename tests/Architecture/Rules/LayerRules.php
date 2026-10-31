<?php

declare(strict_types=1);

namespace App\Tests\Architecture\Rules;

use Arkitect\RuleBuilders\Architecture\Architecture;

final readonly class LayerRules implements RulesInterface
{
    public static function rules(): array
    {
        // Component Architecture Rules
        $layeredArchitectureRules = Architecture::withComponents()

            // Layers
            ->component('Domain')->definedBy('App\Domain\*')
            ->component('Application')->definedBy('App\Application\*')
            ->component('Presentation')->definedBy('App\Presentation\*')
            ->component('Infrastructure')->definedBy('App\Infrastructure\*')

            // Symfony Components
            ->component('ComponentString')->definedBy('Symfony\Component\String')
            ->component('ComponentValidator')->definedBy('Symfony\Component\Validator')
            
            // Rules
            ->where('Domain')->shouldOnlyDependOnComponents('Domain', 'ComponentValidator', 'ComponentString')
            ->where('Application')->mayDependOnComponents(componentNames: 'Domain')
            ->where('Presentation')->mayDependOnComponents('Application')
            ->where('Infrastructure')->mayDependOnComponents('Domain', 'Application')

            ->rules();

        return iterator_to_array($layeredArchitectureRules);
    }
}

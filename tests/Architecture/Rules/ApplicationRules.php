<?php

declare(strict_types=1);

namespace App\Tests\Architecture\Rules;

use Arkitect\Expression\ForClasses\IsFinal;
use Arkitect\Expression\ForClasses\IsReadonly;
use Arkitect\Expression\ForClasses\MatchOneOfTheseNames;
use Arkitect\Expression\ForClasses\NotHaveNameMatching;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;

final readonly class ApplicationRules implements RulesInterface
{
    public static function rules(): array
    {
        $rules = [];

        $rules[] = Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('App\Application\Query'))
            ->andThat(new NotHaveNameMatching('QueryCache'))
            ->should(new MatchOneOfTheseNames(['*Query', '*QueryHandler']))
            ->because('queries must respect naming conventions');

        $rules[] = Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('App\Application\Commmand'))
            ->should(new MatchOneOfTheseNames(['*Command', '*CommandHandler']))
            ->because('commands must respect naming conventions');

        $rules[] = Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('App\Application\Query', 'App\Application\Command'))
            ->andThat(new NotHaveNameMatching('*Handler'))
            ->should(new IsFinal())
            ->andShould(new IsReadonly())
            ->because('queries and commands must be immutable objects');

        return $rules;
    }
}

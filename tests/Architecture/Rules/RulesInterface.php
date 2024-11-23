<?php

declare(strict_types=1);

namespace App\Tests\Architecture\Rules;

use Arkitect\Rules\Rule;

interface RulesInterface
{
    /**
     * @return Rule[]
     */
    public static function rules(): array;
}

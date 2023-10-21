<?php

declare(strict_types=1);

namespace App\Infrastructure\Contentful\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Resource
{
    #[SerializedName('__typename')]
    public string $typename;

    public System $sys;
}

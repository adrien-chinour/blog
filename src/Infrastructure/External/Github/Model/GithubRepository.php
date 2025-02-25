<?php

declare(strict_types=1);

namespace App\Infrastructure\External\Github\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class GithubRepository
{
    public string $name;

    #[SerializedName("html_url")]
    public string $htmlUrl;

    public ?string $description = null;

    public ?string $language = null;
}

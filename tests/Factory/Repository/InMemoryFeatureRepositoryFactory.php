<?php

declare(strict_types=1);

namespace App\Tests\Factory\Repository;

use App\Domain\Config\Feature;
use App\Infrastructure\Repository\InMemoryFeatureRepository;

final readonly class InMemoryFeatureRepositoryFactory
{
    public static function create(): InMemoryFeatureRepository
    {
        return new InMemoryFeatureRepository(
            [
                new Feature('aside_recommendations', 'Activer les recommendations', true),
                new Feature('allow_comments', 'Autoriser la soumission de commentaire', false),
                new Feature('aside_comments', 'Activer la zone commentaire', false),
                new Feature('script_faro', 'Activer le script Faro', true),
                new Feature('script_umami', 'Activer le script Umami', false),
                new Feature('transition_image', 'Activer la View Transition sur l\'image de l\'article', false),
                new Feature('transition_title', 'Activer la View Transition sur le titre de l\'article', false),
            ]
        );
    }
}

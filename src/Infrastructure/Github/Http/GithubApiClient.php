<?php

declare(strict_types=1);

namespace App\Infrastructure\Github\Http;

use App\Infrastructure\Github\Model\GithubRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GithubApiClient
{
    public function __construct(
        private HttpClientInterface $githubClient,
        private string              $githubUser,
        private SerializerInterface $serializer,
    ) {}

    /**
     * @return GithubRepository[]
     */
    public function getRepositories(int $limit = 10): array
    {
        try {
            $response = $this->githubClient->request(
                'GET',
                sprintf('/users/%s/repos', $this->githubUser),
                [
                    'query' => [
                        'sort' => 'pushed',
                        'type' => 'public',
                        'per_page' => $limit,
                    ]
                ],
            );

            return $this->serializer->deserialize(
                $response->getContent() ?? [],
                GithubRepository::class . '[]',
                'json',
            );
        } catch (\Throwable $t) {
            dd($t);
            return [];
        }
    }
}

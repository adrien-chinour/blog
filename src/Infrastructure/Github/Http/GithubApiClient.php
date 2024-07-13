<?php

declare(strict_types=1);

namespace App\Infrastructure\Github\Http;

use App\Infrastructure\Github\Model\GithubRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class GithubApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly HttpClientInterface $githubClient,
        private readonly string              $githubUser,
        private readonly SerializerInterface $serializer
    ) {
        $this->logger = new NullLogger();
    }

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

            $result = $this->serializer->deserialize(
                $response->getContent(),
                GithubRepository::class . '[]',
                'json',
            );
        } catch (Throwable $e) {
            $this->logger?->error($e->getMessage(), ['exception' => $e]);
            $result = [];
        }

        Assert::isArray($result);
        Assert::allIsInstanceOf($result, GithubRepository::class);

        return $result;
    }
}

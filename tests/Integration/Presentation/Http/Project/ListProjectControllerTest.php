<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Project;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListProjectControllerTest extends WebTestCase
{
    public function testListProjectWillReturnCollectionOfProject(): void
    {
        $client = static::createClient();

        $client->request('GET', '/projects');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testListProjectWithLimitWillReturnCollectionOfProjectWithSameLimit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/projects', ['limit' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $payload = json_decode($response->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);
    }
}

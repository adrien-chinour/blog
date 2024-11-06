<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListFeatureControllerTest extends WebTestCase
{
    public function testListFeatureWillReturnCollectionOfFeature(): void
    {
        $client = static::createClient();

        $client->request('GET', '/features');

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }
}

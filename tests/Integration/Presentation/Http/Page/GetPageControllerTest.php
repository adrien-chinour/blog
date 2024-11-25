<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Page;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetPageControllerTest extends WebTestCase
{
    public function testGetPageWillReturnJsonPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pages', [
            'path' => '/about/',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');
    }

    public function testGetPageWillReturnNotFoundOnUnknownUrl(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);

        $client->request('GET', '/pages', [
            'path' => '/unknown/',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}

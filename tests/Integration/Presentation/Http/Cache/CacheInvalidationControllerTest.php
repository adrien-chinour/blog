<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Cache;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CacheInvalidationControllerTest extends WebTestCase
{
    private const string URI = '/cache/invalidation?tag[]=article';

    public function testCacheInvalidationRequiresAuthentication(): void
    {
        $client = self::createClient();

        $client->request('POST', self::URI);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseFormatSame('json');
    }

    public function testCacheInvalidationRejectsInvalidToken(): void
    {
        $client = self::createClient();

        $client->request('POST', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer WrongToken']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCacheInvalidationWithAdminTokenSucceeds(): void
    {
        $client = self::createClient();

        $client->request('POST', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer TestToken']);

        $this->assertResponseIsSuccessful();
    }

    public function testCacheInvalidationIsNotExposedOnGet(): void
    {
        $client = self::createClient();

        $client->request('GET', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer TestToken']);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}

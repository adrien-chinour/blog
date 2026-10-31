<?php

declare(strict_types=1);

namespace App\Tests\Integration\Presentation\Http\Cache;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CacheInvalidationControllerTest extends WebTestCase
{
    private const URI = '/cache/invalidation?tag[]=article';

    public function testCacheInvalidationRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('POST', self::URI);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseFormatSame('json');
    }

    public function testCacheInvalidationRejectsInvalidToken(): void
    {
        $client = static::createClient();

        $client->request('POST', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer WrongToken']);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCacheInvalidationWithAdminTokenSucceeds(): void
    {
        $client = static::createClient();

        $client->request('POST', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer TestToken']);

        $this->assertResponseIsSuccessful();
    }

    public function testCacheInvalidationIsNotExposedOnGet(): void
    {
        $client = static::createClient();

        $client->request('GET', self::URI, server: ['HTTP_AUTHORIZATION' => 'Bearer TestToken']);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}

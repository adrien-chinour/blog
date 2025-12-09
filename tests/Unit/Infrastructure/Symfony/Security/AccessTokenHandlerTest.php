<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Symfony\Security;

use App\Infrastructure\Symfony\Security\AccessTokenHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class AccessTokenHandlerTest extends TestCase
{
    private const VALID_TOKEN = 'valid-admin-token';

    public function testGetUserBadgeFromReturnsUserBadgeWithValidToken(): void
    {
        $handler = new AccessTokenHandler(self::VALID_TOKEN);

        $badge = $handler->getUserBadgeFrom(self::VALID_TOKEN);

        $this->assertInstanceOf(UserBadge::class, $badge);
        $this->assertSame('admin', $badge->getUserIdentifier());
    }

    public function testGetUserBadgeFromThrowsExceptionWithInvalidToken(): void
    {
        $handler = new AccessTokenHandler(self::VALID_TOKEN);

        $this->expectException(BadCredentialsException::class);
        $this->expectExceptionMessage('access token is invalid');

        $handler->getUserBadgeFrom('invalid-token');
    }

    public function testGetUserBadgeFromThrowsExceptionWithEmptyToken(): void
    {
        $handler = new AccessTokenHandler(self::VALID_TOKEN);

        $this->expectException(BadCredentialsException::class);

        $handler->getUserBadgeFrom('');
    }

    public function testGetUserBadgeFromIsCaseSensitive(): void
    {
        $handler = new AccessTokenHandler(self::VALID_TOKEN);

        $this->expectException(BadCredentialsException::class);

        $handler->getUserBadgeFrom('VALID-ADMIN-TOKEN');
    }

    public function testGetUserBadgeFromWithDifferentValidTokens(): void
    {
        $handler1 = new AccessTokenHandler('token-1');
        $handler2 = new AccessTokenHandler('token-2');

        $badge1 = $handler1->getUserBadgeFrom('token-1');
        $badge2 = $handler2->getUserBadgeFrom('token-2');

        $this->assertInstanceOf(UserBadge::class, $badge1);
        $this->assertInstanceOf(UserBadge::class, $badge2);
        $this->assertSame('admin', $badge1->getUserIdentifier());
        $this->assertSame('admin', $badge2->getUserIdentifier());
    }
}


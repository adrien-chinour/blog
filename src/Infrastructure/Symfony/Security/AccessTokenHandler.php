<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(#[\SensitiveParameter] private readonly string $adminToken) {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        if ($accessToken !== $this->adminToken) {
            throw new BadCredentialsException('access toekn is invalid');
        }

        return new UserBadge('admin');
    }
}

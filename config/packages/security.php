<?php

use App\Infrastructure\Symfony\Security\AccessTokenHandler;
use Symfony\Config\SecurityConfig;

const ADMIN_USER_PROVIDER = 'admin_user_provider';

return static function (SecurityConfig $security): void {
    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $firewall = $security->firewall('main')
        ->lazy(true)
        ->security(true)
        ->stateless(true)
        ->provider(ADMIN_USER_PROVIDER);

    $firewall
        ->accessToken()
        ->tokenHandler(AccessTokenHandler::class);

    $firewall->loginThrottling()
        ->maxAttempts(3)
        ->interval('60 minutes');

    $security->provider(ADMIN_USER_PROVIDER)
        ->memory()
        ->user('admin')
        ->password(null)
        ->roles(['ROLE_ADMIN']);
};

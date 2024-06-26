<?php

use App\Infrastructure\Symfony\Security\AccessTokenHandler;
use Symfony\Config\SecurityConfig;

const ADMIN_USER_PROVIDER = 'admin_user_provider';

return static function (SecurityConfig $security): void {
    $security->provider(ADMIN_USER_PROVIDER)
        ->memory()
        ->user('admin')
        ->password(null)
        ->roles(['ROLE_ADMIN']);

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $adminFirewall = $security->firewall('admin')
        ->pattern('^/(webhook)/')
        ->security(true)
        ->stateless(true)
        ->provider(ADMIN_USER_PROVIDER);

    $adminFirewall
        ->accessToken()
        ->tokenHandler(AccessTokenHandler::class);

    $adminFirewall->loginThrottling()
        ->maxAttempts(3)
        ->interval('60 minutes');

    $security->firewall('main')
        ->lazy(true);

    $security->accessControl()
        ->path('^/(webhook)')
        ->roles(['ROLE_ADMIN']);
};

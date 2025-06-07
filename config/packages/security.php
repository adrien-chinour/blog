<?php

use App\Infrastructure\Symfony\Security\AccessTokenHandler;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $firewall = $security->firewall('main')
        ->lazy(true)
        ->security(true)
        ->stateless(true)
        ->provider('admin_user_provider');

    $firewall
        ->accessToken()
        ->tokenHandler(AccessTokenHandler::class);

    $firewall->loginThrottling()
        ->maxAttempts(3)
        ->interval('60 minutes');

    $security->provider('admin_user_provider')
        ->memory()
        ->user('admin')
        ->password(null)
        ->roles(['ROLE_ADMIN']);
};

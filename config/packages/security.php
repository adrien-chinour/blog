<?php

use App\Infrastructure\Symfony\Security\AccessTokenHandler;
use Symfony\Config\SecurityConfig;

const ADMIN_USER_PROVIDER = 'admin_user_provider';

return static function (SecurityConfig $security): void {
    $adminUserProvider = $security->provider(ADMIN_USER_PROVIDER)->memory();
    $adminUserProvider->user('admin')
        ->password(null)
        ->roles(['ROLE_ADMIN']);

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $security->firewall('admin')
        ->pattern('^/admin/')
        ->security(true)
        ->provider(ADMIN_USER_PROVIDER)
        ->accessToken()
        ->tokenHandler(AccessTokenHandler::class);

    $security->firewall('main')
        ->lazy(true);

    $security->accessControl()
        ->path('^/admin')
        ->roles(['ROLE_ADMIN']);
};

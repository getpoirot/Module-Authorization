<?php
use Module\Authorization\Services;

return [
    'services' => [
        Services::GuardPlugins         => Services\GuardPluginsService::class,
        Services::AuthenticatorPlugins => Services\AuthenticatorPluginsService::class,
    ],
];

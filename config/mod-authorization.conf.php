<?php
use Module\Authorization\Services\Authenticators\AuthenticatorDefaultService;
use Module\Authorization\Services\AuthenticatorPluginsService;
use Module\Authorization\Services\GuardPluginsService;

return [
    AuthenticatorPluginsService::class => [
        'plugins' => [
            'services' => [
                // Authenticators Services
                'default' => AuthenticatorDefaultService::class,
            ],
        ],
    ],

    GuardPluginsService::class => [
        'plugins' => [
            'services' => [
                // Guards Services
            ],
        ],
    ],
];

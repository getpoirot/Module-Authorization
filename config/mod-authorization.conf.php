<?php
use Module\Authorization\Services\Authenticators\ServiceAuthenticatorDefault;
use Module\Authorization\Services\ServiceAuthenticatorsContainer;
use Module\Authorization\Services\ServiceGuardsContainer;
use Module\HttpRenderer\Services\RenderStrategy\ListenersRenderDefaultStrategy;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;

return [
    \Module\Authorization\Module::CONF => [

        ServiceAuthenticatorsContainer::CONF => [
            'plugins_container' => [
                'services' => [
                    // Authenticators Services
                    'default' => ServiceAuthenticatorDefault::class,
                ],
            ],
        ],

        ServiceGuardsContainer::CONF => [
            'plugins_container' => [
                'services' => [
                    // Guards Services
                ],
            ],
        ],
    ],

    // View Renderer Options
    ListenersRenderDefaultStrategy::CONF_KEY => [
        'themes' => [
            'default' => [
                'layout' => [
                    'exception' => [
                        // Display Authentication Exceptions Specific Template
                        exAuthentication::class => 'error/authorization/401',
                    ],
                ],
            ],
        ],
    ],
];


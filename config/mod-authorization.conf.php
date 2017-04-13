<?php
use Module\Authorization\Guard\GuardRestrictIP;
use Module\Authorization\Services\Authenticators\ServiceAuthenticatorDefault;
use Poirot\Application\Sapi\Server\Http\RenderStrategy\DefaultStrategy\ListenerError;
use Poirot\Application\Sapi\Server\Http\RenderStrategy\ListenersRenderDefaultStrategy;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;


return array(
    \Module\Authorization\Module::CONF_KEY => array(
        'authenticators' => array(
            'services' => array(
                // Authenticators Services
                'default' => ServiceAuthenticatorDefault::class,
            ),
        ),
        'guards' => array(
            'services' => array(
                // Guards Services
            ),
        ),
        'options' => array(
            // Settings Used By Services While Factory
            GuardRestrictIP::class => array(
                // Setting Options Provided for Guard
                'block_list' => array(
                    //  '172.19.0.1',
                ),
            ),
        ),
    ),

    // View Renderer Options
    ListenersRenderDefaultStrategy::CONF_KEY
    => array(
        ListenerError::CONF_KEY => array(
            // Display Authentication Exceptions Specific Template
            exAuthentication::class => 'error/authorization/401',
        ),
    ),
);

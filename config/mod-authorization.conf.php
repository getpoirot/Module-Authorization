<?php
use Module\Authorization\Services\Authenticators\ServiceAuthenticatorDefault;
use Module\Authorization\Services\ServiceAuthenticatorsContainer;
use Module\Authorization\Services\ServiceGuardsContainer;
use Poirot\Application\Sapi\Server\Http\RenderStrategy\DefaultStrategy\ListenerError;
use Poirot\Application\Sapi\Server\Http\RenderStrategy\ListenersRenderDefaultStrategy;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;


return array(
    \Module\Authorization\Module::CONF_KEY => array(
        ServiceAuthenticatorsContainer::CONF => array(
            'plugins_container' => array(
                'services' => array(
                    // Authenticators Services
                    'default' => ServiceAuthenticatorDefault::class,
                ),
            ),
        ),
        ServiceGuardsContainer::CONF => array(
            'plugins_container' => array(
                'services' => array(
                    // Guards Services
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

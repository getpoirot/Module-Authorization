<?php
use Module\Authorization\Actions\ServiceAuthenticatorAction;

/**
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return array(
    'services' => array(
        'Authenticator' => ServiceAuthenticatorAction::class,
    ),
);

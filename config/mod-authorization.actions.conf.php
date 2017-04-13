<?php
use Poirot\Ioc\Container\BuildContainer;


/**
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return array(
    'services' => array(
        'Authenticator' => \Module\Authorization\Module\ServiceAuthenticatorAction::class,
    ),
);

<?php
/**
 * Default Authorization IOC Services
 *
 * @see \Poirot\Ioc\Container\BuildContainer
 *
 * ! These Services Can Be Override By Name (also from other modules).
 *   Nested in IOC here at: /module/authorization/services
 *
 *
 * @see \Module\Authorization::getServices()
 */

use Module\Authorization\Services\ServiceAuthenticatorsContainer;
use Module\Authorization\Services\ServiceGuardsContainer;

return [
    'services' => [
        'ContainerGuards'         => ServiceGuardsContainer::class,
        'ContainerAuthenticators' => ServiceAuthenticatorsContainer::class,
    ],
];
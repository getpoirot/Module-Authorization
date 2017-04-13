<?php
namespace Module\Authorization\Services;

use Poirot\Ioc\Container\Service\aServiceContainer;


class ServiceAuthenticatorsContainer
    extends aServiceContainer
{
    const NAME = 'ContainerAuthenticators';

    /** @var string Service Name */
    protected $name = self::NAME;


    /**
     * Create Service
     *
     * @return ContainerAuthenticatorsCapped
     */
    function newService()
    {
        $plugins = new ContainerAuthenticatorsCapped;
        return $plugins;
    }
}

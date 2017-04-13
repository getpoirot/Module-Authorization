<?php
namespace Module\Authorization\Services;

use Poirot\Ioc\Container\Service\aServiceContainer;


class ServiceGuardsContainer
    extends aServiceContainer
{
    const NAME = 'ContainerGuards';

    /** @var string Service Name */
    protected $name = self::NAME;


    /**
     * Create Service
     *
     * @return ContainerGuardsCapped
     */
    function newService()
    {
        $plugins = new ContainerGuardsCapped;
        return $plugins;
    }
}

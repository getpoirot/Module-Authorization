<?php
namespace Module\Authorization\Services;

use Module\Authorization\Interfaces\iGuard;
use Poirot\Ioc\Container\aContainerCapped;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Exception\exContainerInvalidServiceType;
use Poirot\Ioc\Container\Service\ServicePluginLoader;
use Poirot\Loader\LoaderMapResource;


class ContainerGuardsCapped
    extends aContainerCapped
{
    protected $_map_resolver_options = [
        #'plain'   => PathToClass::class,
    ];


    /**
     * Construct
     *
     * @param BuildContainer $cBuilder
     *
     * @throws \Exception
     */
    function __construct(BuildContainer $cBuilder = null)
    {
        $this->_attachDefaults();

        parent::__construct($cBuilder);
    }

    /**
     * Validate Plugin Instance Object
     *
     * @param mixed $pluginInstance
     *
     * @throws \Exception
     */
    function validateService($pluginInstance)
    {
        if (!is_object($pluginInstance))
            throw new \Exception(sprintf('Can`t resolve to (%s) Instance.', $pluginInstance));

        if (!$pluginInstance instanceof iGuard)
            throw new exContainerInvalidServiceType('Invalid Plugin Of Content Object Provided.');

    }


    // ..

    protected function _attachDefaults()
    {
        $service = new ServicePluginLoader([
            'resolver_options' => [
                LoaderMapResource::class => $this->_map_resolver_options
            ],
        ]);


        $this->set($service);
    }
}

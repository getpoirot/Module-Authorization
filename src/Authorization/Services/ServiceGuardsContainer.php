<?php
namespace Module\Authorization\Services;

use Poirot\Application\aSapi;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;


class ServiceGuardsContainer
    extends aServiceContainer
{
    const CONF = 'module.authorization.guards';
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
        $conf    = $this->_getConf();

        $builder = new BuildContainer($conf['plugins_container']);
        $plugins = new ContainerGuardsCapped($builder);
        return $plugins;
    }


    // ..

    /**
     * Get Config Values
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function _getConf()
    {
        // retrieve and cache config
        $services = $this->services();

        /** @var aSapi $config */
        $config   = $services->get('/sapi');
        $orig = $config   = $config->config();
        /** @var DataEntity $config */
        $config   = $config->get(\Module\Authorization\Module::CONF, array());

        if (!isset($config[self::CONF]) && !is_array($config[self::CONF]))
            return null;


        $config   = $config[self::CONF];
        return $config;
    }
}

<?php
namespace Module\Authorization\Services;

use Poirot\Application\aSapi;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;


class ServiceAuthenticatorsContainer
    extends aServiceContainer
{
    const CONF = 'module.authorization.authenticators';
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
        $conf    = $this->_getConf();

        $builder = new BuildContainer($conf['plugins_container']);
        $plugins = new ContainerAuthenticatorsCapped($builder);
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
        $config   = $config->get(\Module\Authorization\Module::CONF_KEY, array());

        if (!isset($config[self::CONF]) && !is_array($config[self::CONF]))
            return null;


        $config   = $config[self::CONF];
        return $config;
    }
}

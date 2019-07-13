<?php
namespace Module\Authorization\Services;

use Module\Authorization\Module;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Service\aServiceContainer;


class AuthenticatorPluginsService
    extends aServiceContainer
{
    /**
     * @inheritdoc
     *
     * @return AuthenticatorPlugins
     * @throws \Exception
     */
    function newService()
    {
        $plugins = new AuthenticatorPlugins;
        if ($conf = \Poirot\config(Module::class, self::class))
            (new BuildContainer($conf['plugins']))
                ->build($plugins);

        return $plugins;
    }
}

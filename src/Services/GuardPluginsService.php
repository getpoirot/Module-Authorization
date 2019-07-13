<?php
namespace Module\Authorization\Services;

use Module\Authorization\Module;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Service\aServiceContainer;


class GuardPluginsService
    extends aServiceContainer
{
    /**
     * @inheritdoc
     * @return GuardPlugins
     */
    function newService()
    {
        $plugins = new GuardPlugins;
        if ($conf = \Poirot\config(Module::class, self::class))
            (new BuildContainer($conf['plugins']))
                ->build($plugins);

        return $plugins;
    }
}

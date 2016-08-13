<?php
namespace Module\Authorization;

use Module\Authorization\Module\AuthenticatorFacade;
use Poirot\Application\aSapi;
use Poirot\Application\Sapi;
use Poirot\Application\Interfaces\Sapi\iSapiModule;

use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;
use Poirot\Ioc\Container;
use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;

use Poirot\Std\Interfaces\Struct\iDataEntity;

class Module implements iSapiModule
    , Sapi\Module\Feature\FeatureModuleAutoload
    , Sapi\Module\Feature\FeatureModuleMergeConfig
    , Sapi\Module\Feature\FeatureModuleNestFacade
    , Sapi\Module\Feature\FeatureOnPostLoadModulesGrabServices
{
    const CONF_KEY = 'module.authorization';

    
    /**
     * @inheritdoc
     */
    function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
    {
        #$nameSpaceLoader = \Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class;
        $nameSpaceLoader = 'Poirot\Loader\Autoloader\LoaderAutoloadNamespace';
        /** @var LoaderAutoloadNamespace $nameSpaceLoader */
        $nameSpaceLoader = $baseAutoloader->by($nameSpaceLoader);
        $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
    }

    /**
     * @inheritdoc
     */
    function initConfig(iDataEntity $config)
    {
        return include __DIR__.'/../../config/module.conf.php';
    }

    /**
     * @inheritdoc
     * @return AuthenticatorFacade
     */
    function getModuleAsFacade(Container $nestedModulesContainer = null)
    {
        return new AuthenticatorFacade;
    }

    /**
     * @inheritdoc
     *
     * - Configure Authenticator Module Facade
     *
     * @param Container $services service names must have default value
     * @param aSapi|SapiHttp|SapiCli $sapi
     */
    function resolveRegisteredServices($services = null, $sapi = null)
    {
        ## Build Mongo Client Managements With Merged Configs

        $config = $sapi->config()->get(self::CONF_KEY);

        if ($config) {
            /** @var AuthenticatorFacade $authenticatorFacade */
            $authenticatorFacade = $services->get('/module/authorization');
            $authenticatorFacade->with($config);
        }
    }
}

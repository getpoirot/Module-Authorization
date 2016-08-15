<?php
namespace Module\Authorization;

use Module\Authorization\Module\AuthenticatorFacade;
use Poirot\Application\aSapi;
use Poirot\Application\Interfaces\Sapi;
use Poirot\Application\Interfaces\Sapi\iSapiModule;

use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;
use Poirot\Ioc\Container;
use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;

use Poirot\Std\Interfaces\Struct\iDataEntity;

class Module implements iSapiModule
    , Sapi\Module\Feature\iFeatureModuleAutoload
    , Sapi\Module\Feature\iFeatureModuleMergeConfig
    , Sapi\Module\Feature\iFeatureModuleNestFacade
    , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
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
        $config = $sapi->config()->get(self::CONF_KEY);
        
        /** @var AuthenticatorFacade $authenticatorFacade */
        $authenticatorFacade = $services->get('/module/authorization');
        
        ## Build Authorization Facade With Merged Configs
        if ($config)
            $authenticatorFacade->with($config);
        
        ## Attach Guards Into Events
        foreach ($authenticatorFacade->listGuards() as $guardName)
            $authenticatorFacade->guard($guardName)
                ->attachToEvent($sapi->event());
        
    }
}

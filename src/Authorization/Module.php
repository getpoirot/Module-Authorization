<?php
namespace Module\Authorization;

use Module\Authorization\Module\ListenerHandleAuthException;
use Poirot\Application\aSapi;
use Poirot\Application\Interfaces\Sapi;
use Poirot\Application\Interfaces\Sapi\iSapiModule;
use Poirot\Application\Sapi\Event\EventHeapOfSapi;
use Poirot\Application\SapiCli;
use Poirot\Application\SapiHttp;

use Poirot\Ioc\Container;

use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
use Poirot\Loader\LoaderAggregate;
use Poirot\Loader\LoaderNamespaceStack;

use Poirot\Std\Interfaces\Struct\iDataEntity;

use Module\Authorization\Module\AuthenticatorFacade;


class Module implements iSapiModule
    , Sapi\Module\Feature\iFeatureModuleAutoload
    , Sapi\Module\Feature\iFeatureModuleMergeConfig
    , Sapi\Module\Feature\iFeatureModuleNestFacade
    , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
    , Sapi\Module\Feature\iFeatureModuleInitSapiEvents
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
        $nameSpaceLoader = $baseAutoloader->loader($nameSpaceLoader);
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
     * Attach Listeners To Application Events
     * @see ApplicationEvents
     *
     * priority not that serious
     *
     * @param EventHeapOfSapi $events
     *
     * @return void
     */
    function initSapiEvents(EventHeapOfSapi $events)
    {
        ## handle error pages
        $events->on(
            EventHeapOfSapi::EVENT_APP_ERROR
            , new ListenerHandleAuthException
        );
    }

    /**
     * @inheritdoc
     *
     * - Configure Authenticator Module Facade
     *
     * @param Container              $services service names must have default value
     * @param aSapi|SapiHttp|SapiCli $sapi
     * @param LoaderAggregate        $viewModelResolver
     */
    function resolveRegisteredServices(
        $services = null
        , $sapi = null
        , $viewModelResolver = null
    ) {
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


        ## ViewScripts To View Resolver:
        /** @var LoaderNamespaceStack $resolver */
        $resolver = $viewModelResolver->loader('Poirot\Loader\LoaderNamespaceStack');
        $resolver->with(array(
            'error/401' => __DIR__. '/../../view/error/401',
        ));
    }
}

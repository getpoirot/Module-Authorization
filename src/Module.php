<?php
namespace Module\Authorization
{
    use Module\Authorization\Actions\AuthenticatorAction;
    use Module\Authorization\Application\SapiHttp\ListenerHandleAuthException;
    use Module\Authorization\Services\AuthenticatorPluginsService;
    use Module\Authorization\Services\GuardPluginsService;
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Sapi\Event\EventHeapOfSapi;
    use Poirot\Ioc\Container;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\LoaderAggregate;
    use Poirot\Loader\LoaderNamespaceStack;
    use Poirot\Std\Interfaces\Struct\iDataEntity;


    /**
     * - Services:
     *   > Plugin Container For Authenticators
     *     @see AuthenticatorPluginsService
     *
     *   > Plugin Container For Guards
     *     @see GuardPluginsService
     *
     *
     *   Guards and Authenticators Are Accessible:
     *   \Module\Authorization\Authenticator($authenticator = null)
     *
     *   @see AuthenticatorAction
     *
     *
     * - Guards Attached To Sapi Events While Application Bootstrapped
     *
     */
    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleInitSapi
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
        , Sapi\Module\Feature\iFeatureModuleNestActions
        , Sapi\Module\Feature\iFeatureModuleInitSapiEvents
    {
        /**
         * @inheritdoc
         */
        function initialize($sapi)
        {
            if ( \Poirot\isCommandLine($sapi->getSapiName()) )
                // Sapi Is Not HTTP. SKIP Module Load!!
                return false;
        }

        /**
         * @inheritdoc
         */
        function initAutoload(LoaderAutoloadAggregate $baseAutoloader)
        {
            $nameSpaceLoader = \Poirot\Loader\Autoloader\LoaderAutoloadNamespace::class;
            $nameSpaceLoader = $baseAutoloader->loader($nameSpaceLoader);
            $nameSpaceLoader->addResource(__NAMESPACE__, __DIR__);
        }

        /**
         * @inheritdoc
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-authorization');
        }

        /**
         * @inheritdoc
         */
        function getServices(Container $moduleContainer = null)
        {
            $conf = \Poirot\Config\load(__DIR__ . '/../config/mod-authorization.services');
            return $conf;
        }

        /**
         * @inheritdoc
         *
         * @param LoaderAggregate $viewModelResolver
         * @throws \Exception
         */
        function resolveRegisteredServices(
            $viewModelResolver = null
        ) {
            // ViewScripts To View Resolver:
            // looks for errors in this folder
            $resolver = $viewModelResolver->loader( LoaderNamespaceStack::class );
            /** @var LoaderNamespaceStack $resolver */
            $resolver->with(array(
                'error/authorization/' => __DIR__. '/../view/error/authorization',
            ));
        }

        /**
         * @inheritdoc
         */
        function getActions()
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-authorization.actions');
        }

        /**
         * @inheritdoc
         */
        function initSapiEvents(EventHeapOfSapi $events)
        {
            ## Attach Guards Into Events
            $guards = \Module\Authorization\Actions::Authenticator()->listGuards();
            foreach ($guards as $guardName)
                \Module\Authorization\Actions::Authenticator()->guard($guardName)
                    ->attachToEvent($events);


            ## Handle error pages
            $events->on(
                EventHeapOfSapi::EVENT_APP_ERROR
                , new ListenerHandleAuthException
                // before render strategy error handling
                , 900
            );
        }
    }
}

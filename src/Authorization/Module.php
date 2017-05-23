<?php
namespace Module\Authorization
{
    use Module\Authorization\Application\SapiHttp\ListenerHandleAuthException;
    use Poirot\Application\aSapi;
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Sapi\Event\EventHeapOfSapi;
    use Poirot\Application\Sapi\Module\ContainerForFeatureActions;

    use Poirot\Ioc\Container;

    use Poirot\Ioc\Container\BuildContainer;
    use Poirot\Loader\Autoloader\LoaderAutoloadAggregate;
    use Poirot\Loader\Autoloader\LoaderAutoloadNamespace;
    use Poirot\Loader\LoaderAggregate;
    use Poirot\Loader\LoaderNamespaceStack;

    use Poirot\Std\Interfaces\Struct\iDataEntity;


    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleAutoload
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
        , Sapi\Module\Feature\iFeatureModuleNestActions
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
            return \Poirot\Config\load(__DIR__ . '/../../config/mod-authorization');
        }

        /**
         * Get Nested Module Services
         *
         * it can be used to manipulate other registered services by modules
         * with passed Container instance as argument.
         *
         * priority not that serious
         *
         * @param Container $moduleContainer
         *
         * @return null|array|BuildContainer|\Traversable
         */
        function getServices(Container $moduleContainer = null)
        {
            $conf = \Poirot\Config\load(__DIR__ . '/../../config/mod-authorization.services');
            return $conf;
        }

        /**
         * @inheritdoc
         *
         * @param Container              $services service names must have default value
         * @param aSapi $sapi
         * @param LoaderAggregate        $viewModelResolver
         */
        function resolveRegisteredServices(
            $services = null
            , $viewModelResolver = null
        ) {
            ## ViewScripts To View Resolver:
            /** @var LoaderNamespaceStack $resolver */
            $resolver = $viewModelResolver->loader( LoaderNamespaceStack::class );
            $resolver->with(array(
                'error/authorization/' => __DIR__. '/../../view/error/authorization', // looks for errors in this folder
            ));

        }

        /**
         * Get Action Services
         *
         * priority: after GrabRegisteredServices
         *
         * - return Array used to Build ModuleActionsContainer
         *
         * @return array|ContainerForFeatureActions|BuildContainer|\Traversable
         */
        function getActions()
        {
            return \Poirot\Config\load(__DIR__ . '/../../config/mod-authorization.actions');
        }

        /**
         * Attach Listeners To Application Events
         * @see ApplicationEvents
         *
         * priority: Just Before Dispatch Request When All Modules Loaded
         *           Completely
         *
         * @param EventHeapOfSapi $events
         *
         * @return void
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


namespace Module\Authorization
{
    use Module\Authorization\Actions\AuthenticatorAction;


    /**
     * @method static AuthenticatorAction Authenticator($authenticator = null)
     */
    class Actions extends \IOC
    { }
}


namespace Module\Authorization
{

    use Module\Authorization\Services\ContainerAuthenticatorsCapped;
    use Module\Authorization\Services\ContainerGuardsCapped;

    /**
     * @method static ContainerAuthenticatorsCapped ContainerAuthenticators()
     * @method static ContainerGuardsCapped         ContainerGuards()
     */
    class Services extends \IOC
    { }
}

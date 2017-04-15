<?php
namespace Module\Authorization\Actions;

use Poirot\Ioc\Container\Service\aServiceContainer;


class ServiceAuthenticatorAction
    extends aServiceContainer
{
    /** @var string Service Name */
    protected $name = 'Authenticator';


    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        $auths  = \Module\Authorization\Services\IOC::ContainerAuthenticators();
        $guards = \Module\Authorization\Services\IOC::ContainerGuards();

        $action = new AuthenticatorAction($auths, $guards);
        return $action;
    }
}

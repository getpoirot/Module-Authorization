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
        $auths  = \Module\Authorization\Services::ContainerAuthenticators();
        $guards = \Module\Authorization\Services::ContainerGuards();

        $action = new AuthenticatorAction($auths, $guards);
        return $action;
    }
}

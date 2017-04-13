<?php
namespace Module\Authorization\Module;

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
        $auths  = $this->services()->get('/module/authorization/ContainerAuthenticators');
        $guards = $this->services()->get('/module/authorization/ContainerGuards');

        $action = new AuthenticatorAction($auths, $guards);
        return $action;
    }
}

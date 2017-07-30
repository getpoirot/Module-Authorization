<?php
namespace Module\OAuth2\Services\Guards;

use Poirot\Ioc\Container\Service\aServiceContainer;


abstract class aServiceGuard
    extends aServiceContainer
{
    /** @var string Default Registered Authenticator Name */
    protected $authenticatorName = null;


    /**
     * Create Service
     *
     * @return mixed
     */
    // abstract function newService();


    function setAuthenticatorName($name)
    {
        $this->authenticatorName = $name;
    }


    // ..

    /**
     * Attain Registered Authenticator By It's Name
     *
     * @param string $name
     *
     * @return \Module\Authorization\Actions\AuthenticatorAction
     */
    protected function _attainAuthenticatorByName($name)
    {
        return \Module\Authorization\Actions::Authenticator($name);
    }
}
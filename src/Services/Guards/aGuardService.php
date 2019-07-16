<?php
namespace Module\Authorization\Services\Guards;

use Module\Authorization\Interfaces\iGuard;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\Ioc\Container\Service\aServiceContainer;


abstract class aGuardService
    extends aServiceContainer
{
    /** @var string Default Registered Authenticator Name */
    protected $authenticatorName = null;


    /**
     * Authenticator Name Related To This Guard
     *
     * @param string $name
     */
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
     * @return iAuthenticator
     * @throws \Exception
     */
    protected function _attainAuthenticatorByName($name = null)
    {
        if (! $name = $name ?? $this->authenticatorName)
            throw new \Exception('Authenticator name is not set.');

        return \Module\Authorization\Actions::Authenticator($name);
    }
}

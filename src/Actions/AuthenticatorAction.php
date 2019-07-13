<?php
namespace Module\Authorization\Actions;

use Module\Authorization\Interfaces\iGuard;
use Module\Authorization\Services\AuthenticatorPlugins;
use Module\Authorization\Services\GuardPlugins;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;


class AuthenticatorAction
{
    const AUTHENTICATOR_DEFAULT = 'default';

    /** @var AuthenticatorPlugins */
    protected $authenticators;
    /** @var GuardPlugins */
    protected $guards;


    /**
     * AuthenticatorAction
     *
     * @param $authenticators @IoC /module/authorization/services/ContainerAuthenticators
     * @param $guards         @IoC /module/authorization/services/ContainerGuards
     */
    function __construct(AuthenticatorPlugins $authenticators, GuardPlugins $guards)
    {
        $this->authenticators = $authenticators;
        $this->guards = $guards;
    }

    /**
     * Invoke as Callable
     *
     * @param null|string $authenticator Authenticator name
     *
     * @return $this|iAuthenticator|Authenticator
     * @throws \Exception
     */
    function __invoke($authenticator = null)
    {
        if ($authenticator !== null)
            return $this->authenticator($authenticator);

        return $this;
    }


    /**
     * Retrieve Registered Authenticator Service By Name
     * 
     * @param string $authenticatorName
     * 
     * @return iAuthenticator|Authenticator
     * @throws \Exception
     */
    function authenticator($authenticatorName = self::AUTHENTICATOR_DEFAULT)
    {
        if (! $this->authenticators->has($authenticatorName) )
            throw new \Exception(sprintf('Authenticator (%s) Not Registered.', $authenticatorName));

        return $this->authenticators->get($authenticatorName);
    }

    /**
     * List Registered Authenticators Name
     * 
     * @return string[]
     */
    function listAuthenticators()
    {
        return $this->authenticators->listServices();
    }

    /**
     * Retrieve Authorization Guard
     * 
     * @param string $authorizeOfGuardName
     * 
     * @return iGuard
     * @throws \Exception
     */
    function guard($authorizeOfGuardName)
    {
        if (! $this->guards->has($authorizeOfGuardName) )
            throw new \Exception(sprintf('Guard Authorization (%s) Not Registered.', $authorizeOfGuardName));

        return $this->guards->get($authorizeOfGuardName);
    }

    /**
     * List Registered Authorizations Name
     *
     * @return string[]
     */
    function listGuards()
    {
        return $this->guards->listServices();
    }
}

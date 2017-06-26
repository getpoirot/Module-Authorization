<?php
namespace Module\Authorization\Actions;

use Module\Authorization\Interfaces\iGuard;
use Module\Authorization\Services\ContainerAuthenticatorsCapped;
use Module\Authorization\Services\ContainerGuardsCapped;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;


class AuthenticatorAction
{
    const CONF_AUTHENTICATORS   = 'authenticators';
    const CONF_GUARDS           = 'guards';

    const AUTHENTICATOR_DEFAULT = 'default';


    /** @var ContainerAuthenticatorsCapped */
    protected $authenticators;
    /** @var ContainerGuardsCapped */
    protected $guards;


    /**
     * AuthenticatorAction constructor.
     *
     * @param $authenticators @IoC /module/authorization/ContainerAuthenticators
     * @param $guards         @IoC /module/authorization/ContainerGuards
     */
    function __construct(ContainerAuthenticatorsCapped $authenticators, ContainerGuardsCapped $guards)
    {
        $this->authenticators = $authenticators;
        $this->guards = $guards;
    }

    /**
     *
     * @param null|string $authenticator Authenticator name
     *
     * @return $this
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
        if (!$this->authenticators->has($authenticatorName))
            throw new \Exception(sprintf('Authenticator (%s) Not Registered.', $authenticatorName));

        $authenticator = $this->authenticators->get($authenticatorName);
        return $authenticator;
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

        $guard = $this->guards->get($authorizeOfGuardName);
        return $guard;
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

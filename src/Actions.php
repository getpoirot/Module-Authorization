<?php
namespace Module\Authorization
{
    use Module\Authorization\Actions\AuthenticatorAction;
    use Poirot\AuthSystem\Authenticate\Authenticator;
    use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;


    /**
     * @method static AuthenticatorAction|iAuthenticator|Authenticator Authenticator(string $authenticator = null)
     */
    class Actions extends \IOC
    {
        const Authenticator = 'authenticator';
    }
}

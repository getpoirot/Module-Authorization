<?php
namespace Module\Authorization
{
    use Module\Authorization\Actions\AuthenticatorAction;


    /**
     * @method static AuthenticatorAction Authenticator(string $authenticator = null)
     */
    class Actions extends \IOC
    {
        const Authenticator = 'authenticator';
    }
}

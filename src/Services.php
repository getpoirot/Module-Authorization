<?php
namespace Module\Authorization
{
    use Module\Authorization\Services\AuthenticatorPlugins;
    use Module\Authorization\Services\GuardPlugins;


    /**
     * @method static GuardPlugins         GuardPlugins()
     * @method static AuthenticatorPlugins AuthenticatorPlugins()
     */
    class Services extends \IOC
    {
        const GuardPlugins         = 'GuardPlugins';
        const AuthenticatorPlugins = 'AuthenticatorPlugins';
    }
}

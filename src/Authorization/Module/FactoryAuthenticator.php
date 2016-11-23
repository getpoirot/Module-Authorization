<?php
namespace Module\Authorization\Module;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;

use Poirot\Std\Interfaces\Pact\ipFactory;

/*
// Define Authenticators
\Module\Authorization\Module\AuthenticatorFacade::CONF_KEY_AUTHENTICATORS 
=> array(

    ## authenticator_name => iAuthenticator | (array) options
    \Module\Authorization\Module\AuthenticatorFacade::AUTHENTICATOR_DEFAULT
    => array(
        // default authentication realm; usually realms are considered as unique!!
        'realm'      => \Poirot\AuthSystem\Authenticate\Identifier\aIdentifier::DEFAULT_REALM,
        #O# identifier => iIdentifier | (array) options of aIdentifier
        'identifier' => array(
            // identifier like: session, http digest
            \Poirot\Config\INIT_INS   => array(
                #\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth::class,
                '\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth',
                'options' => array(
                    #O# adapter => iIdentityCredentialRepo | (array) options of CredentialRepo
                    'credential_adapter' => array(
                        \Poirot\Config\INIT_INS => array(
                            'Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile',
                            'options' => array(
                                'pwd_file_path' => __DIR__.'/../data/users.pws',
                            )
                        )
                    )
                ),
            ),

        ),
        #O# adapter => iIdentityCredentialRepo | (array) options of CredentialRepo
        'adapter'    => array(
            // credential adapter, must fulfill identity of identifier * optional
            \Poirot\Config\INIT_INS   => array(
                #\Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile::class,
                '\Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile',
                'options' => array(
                    'pwd_file_path' => __DIR__.'/../data/users.pws',
                ),
            ),
        ),
    ),

    // Authenticator Names Are Unique
    // ...
),
*/

class FactoryAuthenticator
    implements ipFactory
{
    /**
     * Factory With Valuable Parameter
     *
     * @param array $options
     *
     * @return iAuthenticator
     * @throws \Exception
     */
    static function of($options)
    {
        /// TODO move newInitIns to more general place; in exp. when merged config loading complete or something
        //-      all classes and services get instanciated options
        $options    = \Poirot\Ioc\newInitIns($options);
        if ($options instanceof iAuthenticator)
            return $options;


        $realm      = \Poirot\Std\emptyCoalesce(@$options['realm']);
        $identifier = \Poirot\Std\emptyCoalesce(@$options['identifier']);
        $adapter    = \Poirot\Std\emptyCoalesce(@$options['adapter']);

        if ($realm === null || $identifier === null)
            throw new \Exception('Config Provided for (%s) not include "realm" or "adapter".');


        $identifier->setRealm($realm);
        $authenticator = new Authenticator($identifier, $adapter);
        return $authenticator;
    }
}

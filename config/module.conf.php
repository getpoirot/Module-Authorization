<?php
return array(
    Module\Authorization\Module::CONF_KEY => array(
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
                    #'class'  => \Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpDigestAuth::class,
                    'class'   => '\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpDigestAuth',
                    'options' => array(
                        // ...
                    ),
                ),
                #O# adapter => iIdentityCredentialRepo | (array) options of CredentialRepo
                'adapter'    => array(
                    // credential adapter, must fulfill identity of identifier * optional
                    #'class' => \Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile::class,
                    'class'   => '\Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile',
                    'options' => array(
                        'pwd_file_path' => __DIR__.'/../data/users.pws',
                    ),
                ),
            ),

            // Authenticator Names Are Unique


        ),
    ),
);

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
                    '_class_'   => array(
                        #\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth::class,
                        '\Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth',
                        'options' => array(
                            #O# adapter => iIdentityCredentialRepo | (array) options of CredentialRepo
                            'credential_adapter' => array(
                                '_class_' => array(
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
                    '_class_'   => array(
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

        
        // Define Guards
        \Module\Authorization\Module\AuthenticatorFacade::CONF_KEY_GUARDS => array(
            'restrict_ip' => array(
                '_class_' => array(
                    \Module\Authorization\Guard\GuardRestrictIP::class,
                    'options' => array(
                        // Setting Options Provided for Guard
                        'block_list' => array(
                            // '172.19.0.1',
                        ),
                    ),
                )
            ),
            'guard_routes' => array(
                '_class_' => array(
                    \Module\Authorization\Guard\GuardRoute::class,
                    'options' => array(
                        'authenticator' => \Module\Authorization\Module\AuthenticatorFacade::AUTHENTICATOR_DEFAULT,
                    ),
                )
            ),
        ),
    ),

    // View Renderer Options
    \Poirot\Application\Sapi\Server\Http\ViewRenderStrategy\ListenersRenderDefaultStrategy::CONF_KEY
    => array(
        \Poirot\Application\Sapi\Server\Http\ViewRenderStrategy\DefaultStrategy\ListenerError::CONF_KEY => array(
            // Display Authentication Exceptions Specific Template
            \Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication::class => 'error/401',
        ),
    ),
);

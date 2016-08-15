<?php
namespace Module\Authorization\Module;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Identifier\aIdentifier;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentifier;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentityCredentialRepo;
use Poirot\AuthSystem\Authenticate\RepoIdentityCredential\aIdentityCredentialAdapter;
use Poirot\Std\Interfaces\Pact\ipFactory;

/*
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
        $options    = self::_assertOptions($options);
        $realm      = \Poirot\Std\emptyCoalesce(@$options['realm']);
        $identifier = \Poirot\Std\emptyCoalesce(@$options['identifier']);
        $adapter    = \Poirot\Std\emptyCoalesce(@$options['adapter']);

        if ($realm === null || $identifier === null)
            throw new \Exception('Config Provided for (%s) not include "realm" or "adapter".');

        ## Authenticator Identifier:
        if (!$identifier instanceof iIdentifier) {
            // from options
            $identifier        = self::_assertOptions($identifier);
            $identifierClass   = \Poirot\Std\emptyCoalesce(@$identifier['class']);
            $identifierOptions = \Poirot\Std\emptyCoalesce(@$identifier['options']);

            if (!class_exists($identifierClass))
                throw new \Exception(sprintf('Identifier (%s) Not Found.', \Poirot\Std\flatten($identifierClass)));

            /** @var aIdentifier|iIdentifier $identifier */
            $identifier = new $identifierClass;

            if (!$identifier instanceof iIdentifier)
                throw new \Exception(sprintf('Invalid Identifier (%s).', \Poirot\Std\flatten($identifier)));

            if ($identifierOptions) {
                if (!$identifier instanceof aIdentifier)
                    throw new \Exception(sprintf(
                        'Options Provided For Unknown Identifier (%s).', \Poirot\Std\flatten($identifier)
                    ));

                $identifier->with($identifierOptions, true);
            }
        }

        $identifier->setRealm($realm);

        ## Authenticator Adapter
        if ($adapter !== null) {
            if (!$adapter instanceof iIdentityCredentialRepo) {
                $adapter = self::_assertOptions($adapter);
                $adapterClass = \Poirot\Std\emptyCoalesce(@$adapter['class']);
                $adapterOptions = \Poirot\Std\emptyCoalesce(@$adapter['options']);

                if (!class_exists($adapterClass))
                    throw new \Exception(sprintf('Adapter (%s) Not Found.', \Poirot\Std\flatten($adapterClass)));

                /** @var aIdentityCredentialAdapter|iIdentityCredentialRepo $identifier */
                $adapter = new $adapterClass;

                if (!$adapter instanceof iIdentityCredentialRepo)
                    throw new \Exception(sprintf('Invalid Adapter (%s).', \Poirot\Std\flatten($adapter)));

                if ($adapterOptions) {
                    if (!$adapter instanceof aIdentityCredentialAdapter)
                        throw new \Exception(sprintf(
                            'Options Provided For Unknown Adapter (%s).', \Poirot\Std\flatten($adapter)
                        ));

                    $adapter->import($adapterOptions);
                }
            }
        }

        $authenticator = new Authenticator($identifier, $adapter);
        return $authenticator;
    }

    
    // ..
    
    static protected function _assertOptions($options)
    {
        if ($options instanceof \Traversable)
            $options = \Poirot\Std\cast($options)->toArray();

        if (!is_array($options))
            throw new \InvalidArgumentException(sprintf(
                'Options must be array or Traversable; given: (%s).'
                , \Poirot\Std\flatten($options)
            ));

        return $options;
    }
}

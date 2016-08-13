<?php
namespace Module\Authorization\Module;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Identifier\aIdentifier;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentifier;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentityCredentialRepo;
use Poirot\AuthSystem\Authenticate\RepoIdentityCredential\aIdentityCredentialAdapter;

use Poirot\Std\aConfigurable;

class AuthenticatorFacade
    extends aConfigurable
{
    const CONF_KEY_AUTHENTICATORS = 'authenticators';
    const AUTHENTICATOR_DEFAULT = 'default';
    
    /** @var iAuthenticator[] */
    protected $authenticators;


    /**
     * Retrieve Registered Authenticator Service By Name
     * 
     * @param string $authenticatorName
     * 
     * @return iAuthenticator|Authenticator
     * @throws \Exception
     */
    function byAuth($authenticatorName = self::AUTHENTICATOR_DEFAULT)
    {
        if (!isset($this->authenticators[$authenticatorName]))
            throw new \Exception(sprintf('Authenticator (%s) Not Registered.', $authenticatorName));

        return $this->authenticators[$authenticatorName];
    }


    // Implement Configurable
    
    /**
     * @inheritdoc
     */
    function with($options, $throwException = false)
    {
        if ($options instanceof \Traversable)
            $options = \Poirot\Std\cast($options)->toArray();

        if (!is_array($options))
            throw new \InvalidArgumentException(sprintf(
                'Options must be array or Traversable; given: (%s).'
                , \Poirot\Std\flatten($options)
            ));


        // Register Authenticators
        $Authenticators = \Poirot\Std\emptyCoalesce(@$options[self::CONF_KEY_AUTHENTICATORS]);
        if ($Authenticators)
            $this->_withAuthenticators($Authenticators);


    }


    // ..

    /**
     * Register Authenticators With Options
     *
     * @param array $Authenticators
     *
     * @throws \Exception
     */
    protected function _withAuthenticators(array $Authenticators)
    {
        /*
         * default Array(3) … - Sorted
         *   realm String(12) => Default_Auth
         *   identifier Array(2) …
         *   adapter Array(2) …
         */
        foreach ($Authenticators as $name => $options)
        {
            if ($options instanceof iAuthenticator) {
                $this->authenticators[$name] = $options;
                continue;
            }

            $options    = $this->_assertOptions($options);
            $realm      = \Poirot\Std\emptyCoalesce(@$options['realm']);
            $identifier = \Poirot\Std\emptyCoalesce(@$options['identifier']);
            $adapter    = \Poirot\Std\emptyCoalesce(@$options['adapter']);

            if ($realm === null || $identifier === null)
                throw new \Exception('Config Provided for (%s) not include "realm" or "adapter".');

            ## Authenticator Identifier:
            if (!$identifier instanceof iIdentifier) {
                // from options
                $identifier        = $this->_assertOptions($identifier);
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
                    
                    $identifier->with($identifierOptions);
                }
            }
            
            $identifier->setRealm($realm);

            ## Authenticator Adapter
            if ($adapter !== null) {
                if (!$adapter instanceof iIdentityCredentialRepo) {
                    $adapter = $this->_assertOptions($adapter);
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
            $this->authenticators[$name] = $authenticator;
        }
    }

    protected function _assertOptions($options)
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

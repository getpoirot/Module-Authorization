<?php
namespace Module\Authorization\Module;

use Module\Authorization\Interfaces\iGuard;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;

use Poirot\Std\aConfigurable;


class AuthenticatorFacade
    extends aConfigurable
{
    const CONF_KEY_AUTHENTICATORS = 'authenticators';
    const CONF_KEY_GUARDS         = 'guards';

    const AUTHENTICATOR_DEFAULT   = 'default';


    /** @var array */
    protected $authenticators = array(
        # 'authenticator_name' => iAuthenticator | (array) options
    );

    protected $guards = array(
        # 'guard_name' => iGuard | (array) options
    );


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
        if (!isset($this->authenticators[$authenticatorName]))
            throw new \Exception(sprintf('Authenticator (%s) Not Registered.', $authenticatorName));

        $authenticator = $this->authenticators[$authenticatorName];
        if ($authenticator instanceof iAuthenticator)
            return $authenticator;

        
        // Lazy Load Authenticators
        $authenticator = FactoryAuthenticator::of($authenticator);
        $this->authenticators[$authenticatorName] = $authenticator;
        return $authenticator;
    }

    /**
     * List Registered Authenticators Name
     * 
     * @return string[]
     */
    function listAuthenticators()
    {
        return array_keys($this->authenticators);
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
        if (!isset($this->guards[$authorizeOfGuardName]))
            throw new \Exception(sprintf('Guard Authorization (%s) Not Registered.', $authorizeOfGuardName));

        $guard = $this->guards[$authorizeOfGuardName];
        if ($guard instanceof iGuard)
            return $guard;


        // Lazy Load Guards
        $guard = $this->_factoryGuard($guard);
        $this->guards[$authorizeOfGuardName] = $guard;
        return $guard;
    }

    /**
     * List Registered Authorizations Name
     *
     * @return string[]
     */
    function listGuards()
    {
        return array_keys($this->guards);
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

        # Register Authenticators
        $Authenticators = \Poirot\Std\emptyCoalesce(@$options[self::CONF_KEY_AUTHENTICATORS]);
        if ($Authenticators) {
            foreach ($Authenticators as $name => $authenticatorOptions)
                // Lazy Load Authenticators
                $this->authenticators[$name] = $authenticatorOptions;
        }

        # Register Guards
        $Guards = \Poirot\Std\emptyCoalesce(@$options[self::CONF_KEY_GUARDS]);
        if ($Guards) {
            foreach ($Guards as $name => $guardOptions)
                // Lazy Load Guards
                $this->guards[$name] = $guardOptions;
        }
    }

    /**
     * Factory Guard
     *
     * @param $options
     *
     * @return iGuard
     * @throws \Exception
     */
    protected function _factoryGuard($options)
    {
        if ($options instanceof \Traversable)
            $options = \Poirot\Std\cast($options)->toArray();

        if (!is_array($options))
            throw new \InvalidArgumentException(sprintf(
                'Options must be array or Traversable; given: (%s).'
                , \Poirot\Std\flatten($options)
            ));

        $guardOptions = \Poirot\Std\emptyCoalesce(@$options['_class_']['options']);
        if ($guardOptions) {
            // Prepare Guard Options To Understandable To Guard Class
            $options['_class_']['options'] = $this->_guardPrepareConfig($guardOptions);
        }

        /** @var iGuard $instance */
        $instance = \Poirot\Config\instanceInitialized($options);
        return $instance;
    }

    protected function _guardPrepareConfig($options)
    {
        # authenticator
        $authenticator = \Poirot\Std\emptyCoalesce(@$options['authenticator']);
        if ( $authenticator && is_string($authenticator) ) {
            // authenticator as string considered for registered name
            // Lets Instance Validation Handle On Guard Setter Method
            $authenticator = $this->authenticator($authenticator);
            $options['authenticator'] = $authenticator;
        }
        
        return $options;
    }
}

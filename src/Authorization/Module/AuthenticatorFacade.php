<?php
namespace Module\Authorization\Module;

use Module\Authorization\Interfaces\iGuard;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\AuthSystem\Authorize\Interfaces\iAuthorize;

use Poirot\Std\aConfigurable;
use Poirot\Std\Interfaces\Pact\ipConfigurable;


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

        $guardClass = \Poirot\Std\emptyCoalesce(@$options['class']);
        if (!$guardClass)
            throw new \InvalidArgumentException(sprintf(
                'Unknown Guard Config.', \Poirot\Std\flatten($options)
            ));

        if (is_string($guardClass))
            $guardClass = new $guardClass;

        if (!$guardClass instanceof iGuard)
            throw new \InvalidArgumentException(sprintf(
                'Guard must instance of iGuard; given: (%s).', \Poirot\Std\flatten($options['class'])
            ));

        $guardOptions = \Poirot\Std\emptyCoalesce(@$options['options']);
        if ($guardOptions) {
            if (!$guardClass instanceof ipConfigurable)
                throw new \Exception(sprintf('Unknown Guard Configurable (%s).', \Poirot\Std\flatten($guardClass)));

            // Prepare Guard Options To Understandable To Guard Class
            $guardOptions = $this->_guardPrepareConfig($guardOptions);
            $guardClass->with($guardOptions, true);
        }

        return $guardClass;
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

        # authorize
        $authorize = \Poirot\Std\emptyCoalesce(@$options['authorize']);

        if ( $authorize && ( is_array($authorize) || $authorize instanceof \Traversable ) ) {
            // authorize as array
            $class = \Poirot\Std\emptyCoalesce(@$authorize['class']);
            if (!$class)
                throw new \InvalidArgumentException(sprintf(
                    'Unknown Authorize Config.', \Poirot\Std\flatten($authorize)
                ));

            if (is_string($class))
                $class = new $class;

            $classOptions = \Poirot\Std\emptyCoalesce(@$authorize['options']);
            if ($classOptions) {
                if (!$class instanceof ipConfigurable)
                    throw new \Exception(sprintf('Unknown Authorize Configurable (%s).', \Poirot\Std\flatten($class)));

                $class->with($classOptions, true);
            }

            // Lets Instance Validation Handle On Guard Setter Method
            $authorize = $class;
            $options['authorize'] = $authorize;
        }

        return $options;
    }
}

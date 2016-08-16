<?php
namespace Module\Authorization\Guard;

use Module\Authorization\Guard\RestrictIP\IdentityAuthorize;

use Poirot\Application\Sapi\Event\EventHeapOfSapi;

use Poirot\AuthSystem\Authenticate\Exceptions\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;

use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\AuthSystem\Authorize\Interfaces\iAuthorizeResource;

use Poirot\Events\Interfaces\iEvent;

use Poirot\Std\ConfigurableSetter;

class GuardRoute
    extends aGuard
{
    /** @var iAuthenticator */
    protected $authenticator;


    /**
     * Is allowed to features?
     *
     * - we can use this method event if no user identified
     *   in case that all users has access on home route from
     *   resource object, but only authorized users has access
     *   on other route names, and only AdminUser has access on
     *   admin route
     *
     * @param IdentityAuthorize  $role
     * @param iAuthorizeResource $resource
     *
     * @return boolean
     */
    function isAllowed(/*iIdentity*/ $role = null, /*iAuthorizeResource*/ $resource = null)
    {
        $isAllowed = false;

        if ($role === null)
            $role = $isAllowed = $this->authenticator->hasAuthenticated();
        
        return $isAllowed;
    }

    /**
     * Attach To Event
     *
     * note: not throw any exception if event type is unknown!
     *
     * @param iEvent|EventHeapOfSapi $event
     *
     * @return $this
     */
    function attachToEvent(iEvent $event)
    {
        if (\Poirot\isCommandLine())
            // Only Work With Http Sapi
            return $this;

        $self = $this;

        $event->on(EventHeapOfSapi::EVENT_APP_ROUTE_MATCH, function() use ($self) {
            $self->_assertAccess();
        });

        return $this;
    }


    // ..

    /**
     * Assert Access
     *
     * @throws exAuthentication Not allowed
     */
    protected function _assertAccess()
    {
        if (!$this->isAllowed())
            throw new exAccessDenied($this->authenticator);
    }


    // Options:

    /**
     * Set Authenticator
     *
     * @param iAuthenticator $authenticator
     *
     * @return $this
     */
    function setAuthenticator(iAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
        return $this;
    }
}

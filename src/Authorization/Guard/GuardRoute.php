<?php
namespace Module\Authorization\Guard;

use Module\Authorization\Guard\RestrictIP\IdentityAuthorize;
use Module\Authorization\Guard\Route\ResourceAuthorize;
use Poirot\Application\Sapi\Event\EventHeapOfSapi;
use Poirot\AuthSystem\Authenticate\Exceptions\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Exceptions\exNotAuthenticated;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticator;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentity;
use Poirot\AuthSystem\Authorize\Interfaces\iResourceAuthorize;
use Poirot\Events\Interfaces\iEvent;
use Poirot\Router\Route\RouteSegment;


class GuardRoute
    extends aGuard
{
    /** @var iAuthenticator */
    protected $authenticator;
    protected $routesDenied = array(
        # 'main/ouath/*',
    );


    /**
     * Is allowed to features?
     *
     * - we can use this method event if no user identified
     *   in case that all users has access on home route from
     *   resource object, but only authorized users has access
     *   on other route names, and only AdminUser has access on
     *   admin route
     *
     * @param IdentityAuthorize|iIdentity          $role
     * @param ResourceAuthorize|iResourceAuthorize $resource
     *
     * @return boolean
     */
    function isAllowed(iIdentity $role = null, iResourceAuthorize $resource = null)
    {
        if ($resource !== null) {
            // check given route is banned for authorized users?
            $route = $resource->getRoute();
            $route = $route->getName();

            if (!$this->_verifyIsBannedRoute($route))
                // Allow access to free zone route
                return true;
        }

        if ($role === null) {
            if ($role = $this->authenticator->hasAuthenticated())
                $role = $role->withIdentity();
        }

        if ($role === false)
            throw new exNotAuthenticated($this->authenticator);

        return $role->isFulfilled();
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

        $event->on(EventHeapOfSapi::EVENT_APP_MATCH_REQUEST
            , function($route_match = null) use ($self) {
                $self->_assertAccess($route_match);
            }
            , /* //todo use constant */ -10 * 10 // run after route match
        );

        return $this;
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

    /**
     * Set Routes By Name That Need Authorized
     *
     * @param array $routesDenied
     *
     * @return $this
     */
    function setRoutesDenied(array $routesDenied)
    {
        foreach ($routesDenied as $route)
            $this->addRouteDenied($route);

        return $this;
    }

    /**
     * Add Denied Route
     *
     * @param string $routeName
     *
     * @return $this
     */
    function addRouteDenied($routeName)
    {
        array_push($this->routesDenied, (string) $routeName);
        return $this;
    }


    // ..

    /**
     * Assert Access
     *
     * @param RouteSegment $route_match
     */
    protected function _assertAccess($route_match)
    {
        if (! $route_match )
            // only check for route access
            return;


        $resource = new ResourceAuthorize();
        $resource->setRoute($route_match);
        if (!$this->isAllowed(null, $resource)) // determine current authenticated user
            throw new exAccessDenied($this->authenticator);
    }

    /**
     * Check given route name is in banned list
     *
     * @param string $currentRoute
     *
     * @return bool
     */
    function _verifyIsBannedRoute($currentRoute)
    {
        $r = false;

        $currentRoute = rtrim($currentRoute, '/');
        foreach ($this->routesDenied as $deniedRoute) {
            $deniedRoute = rtrim($deniedRoute, '/');
            $allowLeft = false;
            if (substr($deniedRoute, -1) == '*') {
                // remove star
                $allowLeft = true;
                $deniedRoute = substr($deniedRoute, 0, strlen($deniedRoute) -1 );
            }

            if ( ($left = str_replace($deniedRoute, '', $currentRoute)) !== $currentRoute ) {
                if ($allowLeft || $left == '')
                    return true;
            }
        }

        return $r;
    }

}

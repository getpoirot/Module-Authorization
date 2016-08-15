<?php
namespace Module\Authorization\Guard;

use Module\Authorization\Guard\RestrictIP\IdentityAuthorize;

use Poirot\Application\Sapi\Event\EventHeapOfSapi;

use Poirot\AuthSystem\Authenticate\Exceptions\exAccessDenied;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;

use Poirot\AuthSystem\Authorize\Interfaces\iAuthorizeResource;

use Poirot\Events\Interfaces\iEvent;

use Poirot\Std\ConfigurableSetter;

class GuardRestrictIP
    extends aGuard
{
    /** @var array */
    protected $blockList;


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
        if ($role === null) {
            // Recognize Role Itself
            $role = new IdentityAuthorize();
            $role->setIp($_SERVER['REMOTE_ADDR']);
        }

        $ip = $role->getIp();
        return !(in_array($ip, $this->blockList));
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
            // Restriction IP Only Work With Http Sapi
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
            throw new exAccessDenied;
    }


    // Options:

    /**
     * Set IP Block List
     *
     * @param array|\Traversable $list
     *
     * @return $this
     */
    function setBlockList($list)
    {
        if ($list instanceof \Traversable)
            $list = \Poirot\Std\cast($list)->toArray();

        if (!is_array($list))
            throw new \InvalidArgumentException(sprintf(
                'List must instanceof Traversable or array; given (%s).'
                , \Poirot\Std\flatten($list)
            ));

        $this->blockList = $list;
        return $this;
    }
}

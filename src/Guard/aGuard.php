<?php
namespace Module\Authorization\Guard;

use Module\Authorization\Interfaces\iGuard;
use Poirot\Application\Sapi\Event\EventHeapOfSapi;
use Poirot\Events\Interfaces\iEvent;
use Poirot\Std\ConfigurableSetter;


abstract class aGuard
    extends ConfigurableSetter
    implements iGuard
{
    /**
     * Attach To Event
     *
     * note: not throw any exception if event type is unknown!
     *
     * @param iEvent|EventHeapOfSapi $event
     *
     * @return $this
     * @throws \Exception
     */
    function attachToEvent(iEvent $event)
    {
        // It`s may not implemented!!
        // All Guards May Not Attach To Any Event
        throw new \Exception('Not Implemented.');
    }
}

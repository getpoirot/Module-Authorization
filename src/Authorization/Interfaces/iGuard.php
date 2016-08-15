<?php
namespace Module\Authorization\Interfaces;

use Poirot\AuthSystem\Authorize\Interfaces\iAuthorize;
use Poirot\Events\Interfaces\iCorrelatedEvent;

interface iGuard
    extends 
    iAuthorize
    , iCorrelatedEvent // attach to sapi events for guard resources
    
{
    
}

<?php
namespace Module\Authorization\Guard\Route;

use Poirot\AuthSystem\Authorize\Interfaces\iResourceAuthorize;
use Poirot\Router\Interfaces\iRoute;
use Poirot\Std\Struct\aDataOptions;

class ResourceAuthorize
    extends aDataOptions
    implements iResourceAuthorize
{
    /** @var iRoute */
    protected $route;

    
    /**
     * Set Resource Route
     * 
     * @param iRoute $route
     * 
     * @return $this
     */
    function setRoute(iRoute $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Get Resource Route
     * 
     * @return iRoute|null
     */
    function getRoute()
    {
        return $this->route;
    }
}

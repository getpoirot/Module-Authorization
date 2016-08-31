<?php
namespace Module\Authorization\Guard\RestrictIP;

use Poirot\AuthSystem\Authenticate\Interfaces\iIdentity;

use Poirot\Std\Struct\aDataOptions;


class IdentityAuthorize 
    extends aDataOptions
    implements iIdentity
{
    protected $ip;

    
    /**
     * @return mixed
     */
    function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    function setIp($ip)
    {
        $this->ip = (string) $ip;
    }
}

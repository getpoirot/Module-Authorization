<?php
namespace Module\Authorization\Module;

use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;

use Poirot\Events\Listener\aListener;


class ListenerHandleAuthException
    extends aListener
{
    /**
     * @param \Exception $exception
     *
     * @return void|array
     */
    function __invoke($exception = null)
    {
        if (!$exception instanceof exAuthentication)
            ## unknown error
            return;

        if (!$authenticator = $exception->getAuthenticator())
            // Not Identifier Handle Error!! Let It Go...
            return;

        $authenticator->identifier()->issueException($exception);
    }
}

<?php
namespace Module\Authorization\Application\SapiHttp;

use Module\HttpRenderer\RenderStrategy\aRenderStrategy;
use Poirot\Application\aSapi;
use Poirot\AuthSystem\Authenticate\Exceptions\exAuthentication;
use Poirot\Events\Listener\aListener;


class ListenerHandleAuthException
    extends aListener
{
    /**
     * @param \Exception $exception
     * @param aSapi $sapi
     *
     * @return array|void
     */
    function __invoke($exception = null, $sapi = null)
    {
        /** @var aRenderStrategy $renderStrategy */
        /*
        TODO The Render Strategy Is Deprecated And Not Accessible.
        $renderStrategy = $sapi->services()->get('renderStrategy');
        if (strpos($renderStrategy->getContentType(), 'text/html') === false)
            // Just Handle Html Follows; Lets Other Behind; exp. when renderer is json just response error result!!
            return;
        */

        if (! $exception instanceof exAuthentication )
            ## unknown error
            return;

        if (! $authenticator = $exception->getAuthenticator() )
            // Not Identifier Handle Error!! Let It Go...
            return;

        return $authenticator->identifier()->issueException($exception);
    }
}

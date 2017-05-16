<?php
namespace Module\Authorization\Application\SapiHttp;

use Module\HttpRenderer\Services\RenderStrategy\aListenerRenderStrategy;
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
        /** @var aListenerRenderStrategy $renderStrategy */
        $renderStrategy = $sapi->services()->get('renderStrategy');
        if (strpos($renderStrategy->getContentType(), 'text/html') === false)
            // Just Handle Html Follows; Lets Other Behind; exp. when renderer is json just response error result!!
            return;

        if (! $exception instanceof exAuthentication )
            ## unknown error
            return;

        if (! $authenticator = $exception->getAuthenticator() )
            // Not Identifier Handle Error!! Let It Go...
            return;

        return $authenticator->identifier()->issueException($exception);
    }
}

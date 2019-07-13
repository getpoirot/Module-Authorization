<?php
namespace Module\Authorization\Application\SapiHttp;

use Poirot\Application\aSapi;
use Poirot\AuthSystem\Authenticate\Exceptions\AuthenticationError;
use Poirot\Events\Listener\aListener;
use Poirot\Http\HttpRequest;
use Poirot\Http\Interfaces\iHeader;


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
        $continue = false;

        /** @var HttpRequest $request */
        $request = $sapi->services()->get('/httpRequest');
        if ( $request->headers()->has('Accept') ) {
            /** @var iHeader $h */
            foreach ( $request->headers()->get('Accept') as $h ) {
                if ( strpos($h->renderValueLine(), 'text/html') === false )
                    continue;

                // Just Handle Html Follows; Lets Other Behind; exp. when renderer is json just response error result!!
                $continue = true;
                break;
            }
        }

        if (false === $continue)
            return null;


        if (! $exception instanceof AuthenticationError )
            ## unknown error
            return;

        if (! $authenticator = $exception->getAuthenticator() )
            // Not Identifier Handle Error!! Let It Go...
            return;

        return $authenticator->identifier()->issueException($exception);
    }
}

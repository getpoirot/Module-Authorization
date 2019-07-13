<?php
namespace Module\Authorization\Services\Authenticators;

use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Identifier\aIdentifier;
use Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth;
use Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile;
use Poirot\Http\Interfaces\iHttpRequest;
use Poirot\Http\Interfaces\iHttpResponse;
use Poirot\Ioc\Container\Service\aServiceContainer;


class AuthenticatorDefaultService
    extends aServiceContainer
{
    /**
     * @inheritdoc
     *
     * @return Authenticator
     * @throws \Exception
     */
    function newService()
    {
        // Affect Application Request/Response
        $identifier = new IdentifierHttpBasicAuth;
        $identifier
            ->setRequest($this->_httpRequest())
            ->setResponse($this->_httpResponse());

        $adapter = new IdentityCredentialDigestFile;
        $identifier->setCredentialAdapter($adapter);
        $identifier->setRealm(aIdentifier::DEFAULT_REALM);

        $authenticator = new Authenticator($identifier, $adapter);
        return $authenticator;
    }

    // ..

    /**
     * Http Request
     *
     * @return iHttpRequest
     * @throws \Exception
     */
    private function _httpRequest()
    {
        return \IOC::GetIoC()->get('/HttpRequest');
    }

    /**
     * Http Response
     *
     * @return iHttpResponse
     * @throws \Exception
     */
    private function _httpResponse()
    {
        return \IOC::GetIoC()->get('/HttpResponse');
    }
}

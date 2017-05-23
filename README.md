# Module-Authorization

## Module Configuration

```php
# Authorization:

\Module\Authorization\Module::CONF_KEY => array(
    ServiceAuthenticatorsContainer::CONF => array(
        'plugins_container' => array(
            // these configurations are same as container builder settings...
            /** @see BuilderContainer */
            'services' => array(
                'realm_name' => instanceof (Poirot\AuthSystem\Authenticate\Authenticator)
            ),
        ),
    ),
    ServiceGuardsContainer::CONF => array(
        'plugins_container' => array(
            // these configurations are same as container builder settings...
            /** @see BuilderContainer */
            'services' => array(
                // Guards Services
                'guard_name' => instaceof (iGuard),
            ),
        ),
    ),
),

```


## An example of ServiceAuthenticator plugin

```php
/**
 * Authenticator Service That Register in Module Authorize as
 * authenticators capped plugin.
 *
 */
class ServiceAuthenticatorDefault
    extends aServiceContainer
{
    protected $name = \Module\OAuth2\Module::REALM;
    
    
    /**
     * Create Service
     *
     * @return Authenticator
     */
    function newService()
    {
        ## Set Credential Repo Behalf Of Users Repository
        $repoUsers = \Module\OAuth2\Services\Repository\IOC::Users();
        $credentialAdapter = __(new RepoUserPassCredential)->setRepoUsers($repoUsers);


        ### Attain Login Continue If Has
        /** @var iHttpRequest $request */
        $request  = \IOC::GetIoC()->get('/HttpRequest');

        $authenticator = new Authenticator(
            __(new IdentifierWrapIdentityMap(
                // TODO using cookie+session identifier to recognize user and feature to remember me!!
                __(new IdentifierSession)->setIssuerException(function(exAuthentication $e) use ($request) {
                    $loginUrl = (string) \Module\HttpFoundation\Actions::url('main/oauth/login'); // ensure routes loaded
                    $continue = \Module\Foundation\Actions::path(sprintf(
                        '$baseUrl/%s'
                        , ltrim($request->getTarget(), '/'))
                    );
                    $loginUrl .= '?continue='.urlencode($continue);
                    header('Location: '.$loginUrl);
                })
                /** @see Users::findOneMatchBy */
                , new IdentityFulfillmentLazy($repoUsers, 'uid')
            ))->setRealm(aIdentifier::DEFAULT_REALM)
            , $credentialAdapter // Identity Username --------^
        );

        return $authenticator;
    }

    /**
     * @override
     * !! Access Only In Capped Collection; No Nested Containers Here
     *
     * Get Service Container
     *
     * @return ContainerAuthenticatorsCapped
     */
    function services()
    {
        return parent::services();
    }
}
```

## An Example of Http Guard
```php
class ServiceAuthGuard
    extends aServiceContainer
{
    /**
     * Create Service
     *
     * @return GuardRoute
     */
    function newService()
    {
        $guard = new GuardRoute;
        $auth  = \Module\Authorization\Actions::Authenticator( \Module\OAuth2\Module::REALM );
        $guard->setAuthenticator( $auth );
        $guard->setRoutesDenied([
            'main/oauth/authorize',
            'main/oauth/me/*',
        ]);

        return $guard;
    }
}
```

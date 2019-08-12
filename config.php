<?php
define('CONFIG', [
    'clientId' => 'client id',
    'clientSecret' => 'client secret',
    'idTokenIssuer' => 'https://identity-dev.moneyhub.co.uk/oidc',
    'redirectUri' => 'http://your-redirect-ui',
    'urlAuthorize' => 'https://identity-dev.moneyhub.co.uk/oidc/auth',
    'urlAccessToken' => 'https://identity-dev.moneyhub.co.uk/oidc/token',
    'urlResourceOwnerDetails' => 'https://api-dev.moneyhub.co.uk/v2.0',
    'privateKey' => [/* your jwks */],
    'publicKey' => [/* nothing to add here */],
    'scopes' => 'openid offline_access accounts:read transactions:read:all payee:create payee:read reauth refresh',
    'token_endpoint_auth_method' => 'client_secret_basic',
    'id_token_signed_response_alg' => 'RS256',
    'request_object_signing_alg' => 'RS256',
    'responseType' => 'code',
]);

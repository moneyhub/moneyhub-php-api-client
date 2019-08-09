<?php

use \OpenIDConnectClient\OpenIDConnectProvider;
use \Firebase\JWT\JWT;
use \Jose\KeyConverter\RSAKey;
use \GuzzleHttp\Psr7\Request;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use \GuzzleHttp\Client;
use \League\OAuth2\Client\Token\AccessToken;
use \League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;

/**
 * Class MoneyhubClient
 */
class MoneyhubClient extends OpenIDConnectProvider
{
    /**
     * @var GuzzleHttp\Client;
     */
    protected $client;

    /**
     * @var string
     */
    protected $privateKeyPEM;

    /**
     * @var array
     */
    protected $privateKeyJWK;


    /**
     * MoneyhubClient constructor.
     * @param array $options
     * @param array $signer
     */
    public function __construct(array $options, array $signer)
    {
        $this->client = new Client();
        parent::__construct($options, $signer);
        $this->fetchPublicKey();
        $this->privateKeyJWK = $options['privateKey'];
        $this->privateKeyPEM = $this->convertJWKToPEM($options['privateKey']);
    }

    /**
     *
     */
    public function fetchPublicKey()
    {
        $discoveryUrl = $this->getIdTokenIssuer() . '/.well-known/openid-configuration';
        $discoveryResult = json_decode(
            $this->client->get($discoveryUrl)->getBody()->getContents(),
            true
        );

        if (isset($discoveryResult['jwks_uri'])) {
            $rawPublicKey = $this
                ->client
                ->get($discoveryResult['jwks_uri'])
                ->getBody()
                ->getContents();

            $publicKeys = json_decode($rawPublicKey, true);
            $this->publicKey = $this->convertJWKToPEM($publicKeys['keys'][0]);
        }
    }

    /**
     * @param array $jwk
     * @return string
     */
    public function convertJWKToPEM(array $jwk): string
    {
        return (new RSAKey($jwk))
            ->toPEM();
    }

    /**
     * @return string
     */
    public function getHttpBasicCredentials(): string
    {
        return base64_encode(sprintf('%s:%s', $this->clientId, $this->clientSecret));
    }

    /**
     * @return string
     */
    public function getPayeeUrl(): string
    {
        return preg_replace('/oidc/', 'payees', $this->idTokenIssuer);
    }

    /**
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return preg_replace('/oidc/', 'payments', $this->idTokenIssuer);
    }

    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return preg_replace('/oidc/', 'request', $this->idTokenIssuer);
    }

    /**
     * @param string $requestUri
     * @return string
     */
    public function getAuthorizeUrlFromRequestUri(string $requestUri): string
    {
        return $this->getBaseAuthorizationUrl() . '?request_uri=' . $requestUri;
    }

    /**
     * @param array $options
     * @return \OpenIDConnectClient\AccessToken
     */
    public function getAccessTokenHttpBasicAuth(array $options)
    {
        $encodedCredentials = $this->getHttpBasicCredentials();
        $request = new Request('POST', $this->getAccessTokenUrl([
        ]),
            [
                'content-type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $encodedCredentials,
            ],
            'scope=' . $options['scope'] . '&grant_type=client_credentials&redirect_uri=' . $this->redirectUri
        );

        return $this->processResponse($request);
    }

    /**
     * @param mixed $grant
     * @param array $options
     * @return AccessToken
     * @throws \OpenIDConnectClient\Exception\InvalidTokenException
     */
    public function getAccessToken($grant, array $options = []): AccessToken
    {
        switch ($grant) {
            case 'client_credentials':
                $token = $this->getAccessTokenHttpBasicAuth($options);
                break;
            default:
                $token = parent::getAccessToken($grant, $options);
        }

        return $token;
    }

    /**
     * @param Request $request
     * @return \OpenIDConnectClient\AccessToken
     */
    public function processResponse(Request $request)
    {
        try {
            $response = $this->getParsedResponse($request);
        } catch (IdentityProviderException $e) {
            var_dump($e);
            exit;
        }
        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }
        $prepared = $this->prepareAccessTokenResponse($response);
        $token = $this->createAccessToken($prepared, $this->grantFactory->getGrant('client_credentials'));

        return $token;
    }

    /**
     * @param string $requestObject
     * @return string
     */
    public function getRequestUri(string $requestObject): string
    {
        $response = $this
            ->client
            ->post(
                $this->getRequestUrl(),
                [
                    'headers' => [
                        'Content-Type' => 'application/jws',
                    ],
                    'body' => $requestObject,
                ]
            )
            ->getBody()
            ->getContents();

        return $response;
    }

    /**
     * @param string|null $scope
     * @param string|null $state
     * @param array|null $claims
     * @param string|null $nonce
     * @return string
     */
    public function requestObject(
        string $scope = null,
        string $state = null,
        array $claims = null,
        string $nonce = null
    ): string
    {
        $token = array_filter([
            'client_id' => $this->clientId,
            'scope' => $scope,
            'state' => $state,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'prompt' => 'consent',
            'claims' => $claims,
            'nonce' => $nonce,
            'exp' => time() + 300,
            'max_age' => 86400,
            'iss' => $this->clientId,
            'aud' => $this->idTokenIssuer,
        ]);

        return JWT::encode($token, $this->privateKeyPEM, 'RS256', $this->privateKeyJWK['kid']);
    }

    /**
     * @param array $options
     * @return string
     */
    public function getPaymentAuthorizationUrl(array $options): string
    {
        $scope = 'payment openid id:' . $options['bankId'];
        $claims = [
            'id_token' => [
                'mh:con_id' => ['essential' => true],
                'mh:payment' => [
                    'essential' => true,
                    'value' => [
                        'amount' => $options['amount'],
                        'payeeRef' => $options['payeeRef'],
                        'payerRef' => $options['payerRef'],
                        'payeeId' => $options['payeeId'],
                    ]
                ]
            ]
        ];
        $signedJWT = $this->requestObject($scope, $options['state'], $claims, $options['nonce']);
        $requestUri = $this->getRequestUri($signedJWT);

        return $this->getAuthorizeUrlFromRequestUri($requestUri) . '&client_id=' . $this->clientId . '&scope=' . urlencode($scope);
    }

    /**
     * @param array $options
     * @return string
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        $claims = [
            'id_token' => [
                'mh:con_id' => ['essential' => true],
                'sub' => ['essential' => true],
            ]
        ];
        $signedJWT = $this->requestObject($options['scope'], $options['state'], $claims, $options['nonce'] ?? null);
        $requestUrl = parent::getAuthorizationUrl([
            'scope' => $options['scope'],
            'request' => $signedJWT,
            'state' => $options['state'],
            'prompt' => 'consent'
        ]);

        return $requestUrl;
    }

    /**
     * @param array $options
     * @return AccessToken|string
     */
    public function exchangeCodeForTokens(array $options)
    {
        $result = '';
        $requestObject = array_filter([
            'state' => $options['state'],
            'code' => $options['code'],
            'id_token' => $options['id_token'],
            'nonce' => $options['nonce'],
        ]);
        try {
            $this->optionProvider = new HttpBasicAuthOptionProvider();
            $result = $this->getAccessToken('authorization_code', $requestObject);
        } catch (\Exception $e) {
            var_dump($e);
        }

        return $result;
    }

    /**
     * @param array $options
     * @return array
     */
    public function getPayments(array $options): array
    {
        try {
            $token = $this->getAccessToken('client_credentials', ['scope' => 'payment:read']);

            $response = $this
                ->client
                ->get(
                    $this->getPaymentUrl(),
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->getToken(),
                        ],
                        GuzzleHttp\RequestOptions::JSON => [
                            'limit' => $options['limit'],
                            'offset' => $options['offset']
                        ],
                    ]
                )
                ->getBody()
                ->getContents();

        } catch (\Exception $e) {
            // Failed to get the access token
            exit($e->getMessage());
        }

        return json_decode($response, true);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getPayment(string $id)
    {
        try {
            $token = $this->getAccessToken('client_credentials', ['scope' => 'payment:read']);
            $response = $this
                ->client
                ->get(
                    $this->getPaymentUrl() . '/' . $id,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->getToken(),
                        ]
                    ]
                )
                ->getBody()
                ->getContents();

        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        return json_decode($response, true);
    }

    /**
     * @param array $options
     * @return array
     */
    public function getPayees(array $options): array
    {
        try {
            $token = $this->getAccessToken('client_credentials', ['scope' => 'payee:read']);

            $response = $this
                ->client
                ->get(
                    $this->getPayeeUrl(),
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->getToken(),
                        ],
                        GuzzleHttp\RequestOptions::JSON => [
                            'limit' => $options['limit'],
                            'offset' => $options['offset']
                        ],
                    ]
                )
                ->getBody()
                ->getContents();

        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        return json_decode($response, true);
    }

    /**
     * @param string $id
     * @return array
     */
    public function getPayee(string $id): array
    {
        try {
            $token = $this->getAccessToken('client_credentials', ['scope' => 'payee:read']);
            $response = $this
                ->client
                ->get(
                    $this->getPayeeUrl() . '/' . $id,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->getToken(),
                        ]
                    ]
                )
                ->getBody()
                ->getContents();

        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        return json_decode($response, true);
    }

    /**
     * @param $accountInformation
     * @return array
     */
    public function addPayee($accountInformation): array
    {
        try {
            $token = $this->getAccessToken('client_credentials', ['scope' => 'payee:create']);
            $response = $this
                ->client
                ->post(
                    $this->getPayeeUrl(),
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->getToken(),
                        ],
                        GuzzleHttp\RequestOptions::JSON => [
                            'accountNumber' => $accountInformation['accountNumber'],
                            'sortCode' => $accountInformation['sortCode'],
                            'name' => $accountInformation['name']
                        ],
                    ]
                )
                ->getBody()
                ->getContents();

        } catch (\Exception $e) {
            // Failed to get the access token
            exit($e->getMessage());
        }

        return json_decode($response, true);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllConnections(): array
    {
        $rawResult = $this->client
            ->request('GET', $this->getIdTokenIssuer() . '/.well-known/all-connections')
            ->getBody()
            ->getContents();

        return json_decode($rawResult, true);
    }
}

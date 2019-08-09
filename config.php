<?php
define('CONFIG', array(
    'clientId' => '46c743fd-aa96-41ad-8043-ac23fa973cc9',
    'clientSecret' => '834cb19e-c42c-4fd4-9429-61d2327dc7b3',
    'idTokenIssuer' => 'https://identity-dev.moneyhub.co.uk/oidc',
    'redirectUri' => 'http://localhost:3001/auth/callback',
    'urlAuthorize' => 'https://identity-dev.moneyhub.co.uk/oidc/auth',
    'urlAccessToken' => 'https://identity-dev.moneyhub.co.uk/oidc/token',
    'urlResourceOwnerDetails' => 'https://api-dev.moneyhub.co.uk/v2.0',
    'privateKey' => array(
        "kty" => "RSA",
        "kid" => "-MWu4tloC7zO4fHVr_zzv0njZE2odfOMASgKVVEA4Zw",
        "use" => "sig",
        "e" => "AQAB",
        "n" => "tCIdCx9mpeEOswEVtsNbsKej3aotxpbRufF2VPwlVEojZpR5JQCIxh91OYwktwW-lpG11uPGs1OCXOFFnfyhZXaRGGG6aWCzUVGB5-Jv8e28HWlM1BvfAqC3Hh-DMuCDG5OSOlInGasEKY6lSKi26b_XcArkChJffKNF8jHeK97rcR2e4CSmzQ0-XrZo3mPs59NIC03R_yAyVNEha9WFDqXyzsf69ARyUyqGu65vUEFRUtL71f_S0SGUqJJsmF03CP_i1EGxtGg02j-6aRHrbYpwCTWEp298E1tA99kCOw_XS1Uos5-mS_CnFnqGh72wlxEXItOrMWvE9NeTBds5wQ",
        "d" => "LztCokf5gkUgtY7zQpi20fsi3Fxi5E9nbnoBrQbwQsmtvpfvq-QX-NsGwbAdcF_xOUm7hDz1PNAIvpHSzzoOl6wfH2WAm2Dfo9LbXRHiiGzthki1_GfScb3yRdO9cvmrZu-qx-ACoJhW3w6oCAU08NV8h8RTRtDKeMPW80pgFuBLTNA76hSAwYRxyMjHIBfrBTc4laugUGy_1Ggp9OQT9PNs8jrxFo42vh2bpPFOeEQLrANDxvQxekdE-bL0Il-N8U1kcNKUzu5vHEDu_JT7jj05llU1HfOzYElBnOPSgrx7rqX6ObgqQ7rq5Hp6-1Y8Relujn-rQ6VsaqP4PzaGAQ",
        "p" => "7_Q_0O0_xAqLzFkJ2hRP0f3W6QlvFXv8W2D3FT5LR3bMsrKxi2ZMFGQV1lZuodDC-NhmVYfYggKTQDjeIzPq5Liw9-lD91z-P4WiwhKy7M-cTkaqBESBLpcS2Bpau_vmERy--DnFKXM9CR4xHLkVHuJ4CrpYSZI-99kqOFMrH5U",
        "q" => "wC3MCSWoqEWG4ObEojPs2StDb4AjiJu8JhI-LwXDUMQS59TwP57kgQcXKmYCfjgvoLNomcId5Noa-V4vjT74CZnLBLAC41ErD2QbIlNznF00F-8i7b9Iny8anbCZseNouZ8Rhz53RSGQHyuxWMwEs5wzM56dzFu1YZsx1xM-Fn0",
        "dp" => "q_7nv1pP2rW0f0VTSn5EuMC6y0930G6O9PEMKq38R71f_LcZjFJHNlaHUJujsVlyZ-y1bZlyF77Azcf1ckZEmK3KK-mITkbZ965Se8nrdtNZtsHwHB7-eebQGxbI8vYccyenu0WtjYiMt3xst6ny-bBbW1U284VgnUeMe9MhlgE",
        "dq" => "FqWO9Mj-Tg4bbedj5qVt1M23Xa1hApkvm-DseQgZ0yu3-p_qS-UCkn3uae2pf4xJeIuL2Qq6ERzurtI9kkyIWEFKXlwLixQmmeK3G91vs058dBGXMSMogLHBVjHYetEMp0sqeqCO15Oz8yhn9sncB_pxQjT_7XQlNMJgJjM7TtU",
        "qi" => "43xMNU8kLe5Oxta42XTbexxDveYsqhuiRsvcgLbg1ZczMaay6oZ6Zqm7KuFn3I5SEl5-HxIrmxWut8ekJdr8xIWz811g8b0vavbbzGFa6-WEM_iAcbKa5c79CnO8XP4mzLBrQc4eXaXui7whfurW1b3YW_s4C6crht6WCekjFbk"
    ),
    'publicKey' => [],
    'scopes' => 'openid offline_access accounts:read transactions:read:all payee:create payee:read reauth refresh',
    'token_endpoint_auth_method' => 'client_secret_basic',
    'id_token_signed_response_alg' => 'RS256',
    'request_object_signing_alg' => 'RS256',
    'responseType' => 'code',
));

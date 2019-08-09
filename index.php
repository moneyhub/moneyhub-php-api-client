<?php

include 'vendor/autoload.php';
include './config.php';
include './defaultValue.php';
include './class/MoneyhubClient.php';

$moneyhubClient = new MoneyhubClient(
    CONFIG,
    [
        'signer' => new \Lcobucci\JWT\Signer\Rsa\Sha256()
    ]
);

switch ($_SERVER['REQUEST_URI']) {
  case '/':
      include './views/home.php';
      break;

  case '/data':
      include './views/all-connections.php';
      break;

  case (preg_match('/\/data\?bankid=.*/', $_SERVER['REQUEST_URI']) ? true : false):
    include './views/connection.php';
    break;
}


if (isset($_GET['code'])) {
  // receive authorization response
  try {
      $token = $moneyhubClient->exchangeCodeForTokens([
          'code' => $_GET['code'],
          'state' => $_GET['state'] ?? DEFAULT_VALUES['DEFAULT_STATE'],
          'nonce' => $_GET['nonce'] ?? DEFAULT_VALUES['DEFAULT_NONCE'],
          'id_token' => $_GET['id_token'] ?? null,
      ]);
  } catch (\OpenIDConnectClient\Exception\InvalidTokenException $e) {
      $errors = $moneyhubClient->getValidatorChain()->getMessages();
      echo $e->getMessage();
      // var_dump($errors);
      return;
  } catch (\Exception $e) {
      echo $e->getMessage();
      $errors = $moneyhubClient->getValidatorChain()->getMessages();
      var_dump($errors);
      return;
  }

  $response = [
      "Token: " . $token->getToken(),
      "Refresh Token: ". $token->getRefreshToken(),
      "Expires: ". $token->getExpires(),
      "Has Expired: ". $token->hasExpired(),
      "All Claims: ". print_r($token->getIdToken()->getClaims(), true)
  ];

  echo join("<br />", $response);
}

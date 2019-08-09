<?php

$scopes = 'openid offline_access id:'.$_GET['bankid'].' accounts:read transactions:read:all';


$redirectUrl = $moneyhubClient->getAuthorizationUrl(
  [
    'scope' => $scopes,
    'state' => DEFAULT_VALUES['DEFAULT_STATE'],
  ]
);
echo '<p><a href="' . $redirectUrl . '">Click me</a></p>';
echo $redirectUrl;

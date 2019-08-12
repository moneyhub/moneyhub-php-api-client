<?php
require_once 'vendor/autoload.php';
include './config.php';
include './defaultValue.php';
include './class/MoneyhubClient.php';

$climate = new League\CLImate\CLImate;

$moneyhubClient = new MoneyhubClient(
    CONFIG,
    [
        'signer' => new \Lcobucci\JWT\Signer\Rsa\Sha256()
    ]
);

$cmd = new Commando\Command();

$cmd->option('code')
    ->aka('c')
    ->required();

$cmd->option('state')
    ->aka('s')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_STATE']);
$cmd->option('payee-id')
    ->aka('p');

$cmd->option('nonce')
    ->aka('n')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_NONCE']);

$cmd->option('id-token')
    ->aka('i');


$result = $moneyhubClient->exchangeCodeForTokens([
    'code' => $cmd['code'],
    'state' => $cmd['state'],
    'nonce' => $cmd['nonce'],
    'id_token' => $cmd['id-token'],
]);

//$climate->dump($result);
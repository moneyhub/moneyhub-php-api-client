<?php
require_once 'vendor/autoload.php';
include './config.php';
include './class/MoneyhubClient.php';

$climate = new League\CLImate\CLImate;

$moneyhubClient = new MoneyhubClient(
    CONFIG,
    [
        'signer' => new \Lcobucci\JWT\Signer\Rsa\Sha256()
    ]
);

$cmd = new Commando\Command();

$cmd->option('limit')
    ->aka('l')
    ->defaultsTo(0);

$cmd->option('offset')
    ->aka('o')
    ->defaultsTo(0);


$result = $moneyhubClient->getPayments([
    'limit' => $cmd['limit'],
    'offset' => $cmd['offset'],
]);

$climate->table($result['data']);
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

$cmd->option('id')
    ->aka('i')
    ->required();

$result = $moneyhubClient->getPayment($cmd['id']);

$climate->json($result);
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

$cmd->option('account-number')
    ->aka('a')
    ->require()
    ->describedAs('8 digits');

$cmd->option('sort-code')
    ->aka('s')
    ->require()
    ->describedAs('6 digits');

$cmd->option('name')
    ->aka('n')
    ->require();

$result = $moneyhubClient->addPayee([
    'accountNumber' => $cmd['account-number'],
    'sortCode' => $cmd['sort-code'],
    'name' => $cmd['name'],
]);

$climate->json($result);
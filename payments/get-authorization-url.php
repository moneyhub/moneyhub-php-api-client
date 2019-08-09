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

$cmd->option('bank-id')
    ->aka('b')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_BANK_ID']);

$cmd->option('amount')
    ->aka('a')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_PAYMENT_AMOUNT']);

$cmd->option('payee-id')
    ->aka('p');

$cmd->option('payee-ref')
    ->aka('e');

$cmd->option('payer-ref')
    ->aka('r');

$cmd->option('state')
    ->aka('s')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_STATE']);

$cmd->option('nonce')
    ->aka('n')
    ->defaultsTo(DEFAULT_VALUES['DEFAULT_NONCE']);


$result = $moneyhubClient->getPaymentAuthorizationUrl([
    'bankId' => $cmd['bank-id'],
    'payeeId' => $cmd['payee-id'],
    'amount' => $cmd['amount'],
    'payeeRef' => $cmd['payee-ref'],
    'payerRef' => $cmd['payee-ref'],
    'state' => $cmd['state'],
    'nonce' => $cmd['nonce'],
]);

$climate->blue($result);
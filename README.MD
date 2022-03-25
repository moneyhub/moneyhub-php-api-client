# DEPRECATED

This repository is no longer supported, while some basic functionality around payees management exists and works, the full flow for completing a payment is not complete.

## Moneyhub PHP API Client

This is a PHP client for the [Moneyhub API](https://moneyhub.github.io/api-docs). It currently supports the following features:

### Introduction

- Getting the list of supported banks
- Getting access tokens and refresh tokens from an authorisation code
- Getting access tokens with client credentials
- Add Payees
- Get Payees and payments

### Installation
This client is not a composer package yet, just a demo code. If you want to try it you need to clone the repository
and run`composer install`

For more information about the config (config.php) see the [Node.JS client documentation](https://github.com/moneyhub/moneyhub-api-client)

### Payment
Don't forget that it's just a demo code so far, if you use the methods addPayee, getPayee, getPayees, getPayment or getPayments
 directly in your code, you are going to log in with the grant type "client_credential". 
authorization_code would be recommended in production.

This is the workflow to create a payment, 
the workflow is explained [on our documentation](https://moneyhub.github.io/api-docs/#payments-2) as well:

**Create a payee (optional, if you already have the payee)**
- CLI:

`php payments/add-payee.php --account-number=12345678 --name="Payee name" --sort-code=123456`
- Code:
```php
<?php
$result = $moneyhubClient->addPayee([
    'accountNumber' => '',
    'sortCode' => '',
    'name' => '',
]);
```
The result is the payee object just created before

**Get Authorisation URL**

You can use the previous payee id to make a payment. 

Example for a payment of £1:
- CLI:

`php payments/get-authorization-url.php --amount=100 --bank-id=xxx --payee-id=xxx --payee-ref="Payee name" --payer="Payer name" --nonce="foo" --state="bar"`

- Code:

```php
<?php
$result = $moneyhubClient->getPaymentAuthorizationUrl([
    'bankId' => 'xxx',
    'payeeId' => 'xxx',
    'amount' => 100,
    'payeeRef' => 'Payee name',
    'payerRef' => 'Payer name',
    'state' => 'bar',
    'nonce' => 'foo',
]);
?>
```

The result is an authorization URL, you can redirect the user to this authorization url, it will redirect to the bank consent page. At this point the payment has 
been created and you can see it if you list all the payments, but we need to confirm the payment now.

**Exchange code for token**

After the payment has been accepted on the bank consent page, you will be redirected to your "redirect_uri" page, with a "code" parameter.

To confirm the payment, you need to exchange this code for a token to our API.
You can have an id_token, if your response_type in the configuration is "code id_token". Otherwise set it to null

- CLI:

`php token/exchange-code-for-token.php --code=code_received --nonce="foo" --state="bar"` 

- Code:

```php
<?php
$moneyhubClient->exchangeCodeForTokens([
    'code' => 'code_received',
    'state' => 'bar',
    'nonce' => 'foo',
    'id_token' => null,
]);
?>
```
Now that the payment is complete, you can see your payments with:

`php payments/get-payments.php`

And the status of a payment with:

`php payments/get-payment.php --id=id_payment_here`

### Usage
You can run each script in the payments and token in CLI, to get more information about their usage
use `--help` option 

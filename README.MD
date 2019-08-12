# Moneyhub API Client

This is a PHP client for the [Moneyhub API](https://moneyhub.github.io/api-docs). It currently supports the following features:

### Introduction

- Getting the list of supported banks
- Generating authorisation urls for new and existing users
- Getting access tokens and refresh tokens from an authorisation code
- Getting access tokens with client credentials
- Getting accounts for a user
- Getting transactions for a user
- Generate authorisation url for payments
- Add Payees
- Get Payees and payments

### Installation
This client is not a composer package yet, just a demo code. If you want to try it you need to clone the repository
and run`composer install`

For more information about the config (config.php) see the [Node.JS client documentation](https://github.com/moneyhub/moneyhub-api-client)

### Usage
You can run each script in the payments and token in CLI, to get more information about their usage
use `--help` option 
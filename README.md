WebwinkelKeur API Client
========================

This is a web client for the WebwinkelKeur API. 

The documentation of the API is available at [https://dashboard.webwinkelkeur.nl/pages/api](https://dashboard.webwinkelkeur.nl/pages/api).

## Installation

You can use `composer` to install the API client into your project:

    composer require webwinkelkeur/client

## Usage

To send requests to the API, you need your WebwinkelKeur ID and authentication token. 

```php
use WebwinkelKeur\Client;
use WebwinkelKeur\Client\Request\Invitation;

$webwinkelKeurClient = new Client($id, $authToken);

$invitation = new Invitation();
$invitation
    ->setCustomerName('John Doe')
    ->setEmailAddress('john.doe@example.com')
    ->setPhoneNumbers(['+1.2024561111', '+1.2024561414'])
    ->setOrderNumber(184553)
    ->setOrderTotal(23.55);

try {
    $webwinkelKeurClient->sendInvitation($invitation);
    echo 'Success!';
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

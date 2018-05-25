WebwinkelKeur API Client
========================

This is a web client for the WebwinkelKeur API.

The documentation of the API is available at https://dashboard.webwinkelkeur.nl/pages/api.

Should you experience any issues with the API client, feel free to open a
[GitHub issue](https://github.com/webwinkelkeur/client/issues).

## Installation

You can use `composer` to install the API client into your project:

    composer require webwinkelkeur/client

## Usage

To send requests to the API, you need your WebwinkelKeur ID and authentication token.

```php
use WebwinkelKeur\Client;
use WebwinkelKeur\Client\Request;

$webwinkelKeurClient = new Client($id, $authToken);
```

### Sending invitations

```php
$invitation = new Request\Invitation();
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

### Retrieving sent invitations

```php
try {
    foreach ($webwinkelKeurClient->getSentInvitations() as $sentInvitation) {
        echo 'Invitation for order ' . $sentInvitation->getOrderNumber()
            . ' was sent on ' . $sentInvitation->getCreatedAt()->format('r')
            . ' to ' . $sentInvitation->getEmail() . "\n";
    }
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

### Retrieving ratings

```php
try {
    foreach ($webwinkelKeurClient->getRatings() as $rating) {
        echo $rating->getName() . ' says "' . $rating->getComment() . "\"\n";
    }
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

### Retrieving ratings summary

```php
try {
    $ratingsSummary = $webwinkelKeurClient->getRatingsSummary();
    echo 'The average rating is ' . $ratingsSummary->getRatingAverage()
        . ' out of ' . $ratingsSummary->getAmount() . " ratings.";
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

### Retrieving web shop details

```php
try {
    $webshop = $webwinkelKeurClient->getWebshop();
    $address = $webshop->getAddress();
    echo $webshop->getName() . ' is located at '
        . $address->getNumber() . ' ' . $address->getStreet() . ', ' . $address->getCity();
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

### Retrieving rich snippet

```php
try {
    $richSnippet = $webwinkelKeurClient->getRichSnippet();
    echo $richSnippet;
} catch (Client\Exception $e) {
    echo $e->getMessage();
}
```

<?php
namespace WebwinkelKeur\Client\Response\Webshop;

use WebwinkelKeur\Client\ResponseAbstract;

class Address extends ResponseAbstract
{
    public function __construct($addressData)
    {
        $this->data['street'] = $addressData->street;
        $this->data['housenumber'] = $addressData->housenumber;
        $this->data['postalcode'] = $addressData->postalcode;
        $this->data['city'] = $addressData->city;
    }

    public function getStreet()
    {
        return $this->data['street'];
    }

    public function getNumber()
    {
        return $this->data['housenumber'];
    }

    public function getPostalCode()
    {
        return $this->data['postalcode'];
    }

    public function getCity()
    {
        return $this->data['city'];
    }
}

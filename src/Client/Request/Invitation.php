<?php
namespace WebwinkelKeur\Client\Request;

use WebwinkelKeur\Client\Exception;
use WebwinkelKeur\Client\RequestAbstract;

/**
 * @see https://dashboard.webwinkelkeur.nl/general/api#api_invitations_add
 * @package WebwinkelKeur\Client\Request
 */
class Invitation extends RequestAbstract
{
    public function validate()
    {
        if (!isset($this->fields['email']) or strpos($this->fields['email'], '@') === false) {
            return false;
        }

        // email
        // order "" order number
        // language en
        // delay
        // customer_name
        // phone_numbers []
        // order_total
        // client // optional name of the software where the request originated
        // platform_version
        // plugin_version

        return true;
    }

    public function setOrderID($orderID)
    {
        if (!$orderID) {
            throw new Exception\ValidationFailed();
        }

        $this->fields['order'] = (string)$orderID;

        return $this;
    }

    public function setOrderTotal($orderTotal)
    {
        if (!is_numeric($orderTotal)) {
            throw new Exception\ValidationFailed($orderTotal);
        }

        $this->fields['order_total'] = $orderTotal;

        return $this;
    }

    public function setDelay($delay)
    {
        $this->fields['delay'] = (int)(string)$delay;

        return $this;
    }

    public function setEmailAddress(string $emailAddress)
    {
        if (strpos($emailAddress, '@') === false) {
            throw new Exception\ValidationFailed($emailAddress);
        }

        $this->fields['email'] = $emailAddress;

        return $this;
    }

    public function setMaxInvitationsPerEmail($number)
    {
        $this->fields['max_invitations_per_email'] = (int)(string)$number;

        return $this;
    }

    public function setPhoneNumbers(array $phoneNumbers)
    {
        $this->fields['phone_numbers'] = $phoneNumbers;

        return $this;
    }

    public function setCustomerName($customerName)
    {
        $this->fields['customer_name'] = (string)$customerName;

        return $this;
    }

    public function setClient($client)
    {
        $this->fields['client'] = (string)$client;

        return $this;
    }

    public function setPlatformVersion($version)
    {
        $this->fields['platform_version'] = (string)$version;

        return $this;
    }

    public function setPluginVersion($pluginVersion)
    {
        $this->fields['plugin_version'] = (string)$pluginVersion;

        return $this;
    }

    public function setLanguage($language)
    {
        $this->fields['language'] = (string)$language;

        return $this;
    }
}
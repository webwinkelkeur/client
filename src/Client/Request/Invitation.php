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
    const DEFAULT_LANGUAGE = 'nl';

    public function validate()
    {
        if (!isset($this->fields['email']) || strpos($this->fields['email'], '@') === false) {
            return false;
        }

        return true;
    }

    public function setOrderNumber($orderNumber)
    {
        if (!$orderNumber) {
            throw new Exception\ValidationFailed("order: $orderNumber");
        }

        $this->fields['order'] = (string)$orderNumber;

        return $this;
    }

    public function setOrderTotal($orderTotal)
    {
        if (!is_numeric($orderTotal) || $orderTotal < 0) {
            throw new Exception\ValidationFailed("order_total: $orderTotal");
        }

        $this->fields['order_total'] = $orderTotal;

        return $this;
    }

    public function setDelay($delay)
    {
        $this->fields['delay'] = (int)(string)$delay;
        if ($this->fields['delay'] < 1) {
            $this->fields['delay'] = 1;
        }

        return $this;
    }

    public function setEmailAddress($emailAddress)
    {
        if (strpos((string)$emailAddress, '@') === false) {
            throw new Exception\ValidationFailed("email: $emailAddress");
        }

        $this->fields['email'] = (string)$emailAddress;

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
<?php
namespace WebwinkelKeur\Client\Response;

use WebwinkelKeur\Client\Exception;
use WebwinkelKeur\Client\ResponseAbstract;

class Webshop extends ResponseAbstract
{
    public function __construct($data)
    {
        $this->data['name'] = $data->name;
        $this->data['address'] = new Webshop\Address($data->address);
        $this->data['logo'] = $data->logo;
        $this->data['languages'] = [];

        foreach ($data->languages as $languageData) {
            $language = new Webshop\Language($languageData);
            $this->data['languages'][$language->getISOCode()] = $language;
        }
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getAddress()
    {
        return $this->data['address'];
    }

    public function getLogo()
    {
        return $this->data['logo'];
    }

    public function getLanguages()
    {
        return $this->data['languages'];
    }

    public function getLanguage($ISOCode)
    {
        if (!isset($this->data['languages'][$ISOCode])) {
            throw new Exception(sprintf('Language not found: %s', $ISOCode));
        }

        return $this->data['languages'][$ISOCode];
    }
}

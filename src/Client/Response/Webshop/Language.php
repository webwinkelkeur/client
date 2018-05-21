<?php
namespace WebwinkelKeur\Client\Response\Webshop;

use WebwinkelKeur\Client\ResponseAbstract;

class Language extends ResponseAbstract
{
    public function __construct($languageData)
    {
        $this->data['name'] = $languageData->name;
        $this->data['url'] = $languageData->url;
        $this->data['iso'] = $languageData->iso;
        $this->data['all'] = (bool)$languageData->all;
        $this->data['main'] = (bool)$languageData->main;
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getURL()
    {
        return $this->data['url'];
    }

    public function getISOCode()
    {
        return $this->data['iso'];
    }

    public function hasAll()
    {
        return $this->data['all'];
    }

    public function isMain()
    {
        return $this->data['main'];
    }
}

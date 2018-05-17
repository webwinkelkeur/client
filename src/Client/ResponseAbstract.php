<?php
namespace WebwinkelKeur\Client;

abstract class ResponseAbstract
{
    abstract public function __construct($data);

    protected $data = [];

    protected function getData($item)
    {
        return isset($this->data[(string)$item]) ? $this->data[(string)$item] : null;
    }
}

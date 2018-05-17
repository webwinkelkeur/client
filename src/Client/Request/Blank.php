<?php
namespace WebwinkelKeur\Client\Request;

use WebwinkelKeur\Client\RequestAbstract;

class Blank extends RequestAbstract
{
    public function validate()
    {
        return true;
    }
}

<?php
namespace WebwinkelKeur\Client;

abstract class RequestAbstract implements RequestInterface
{
    protected $options = [
        'http_errors' => 'false',
        'headers' => [],
    ];
    protected $fields = [];

    public function setHeader($name, $value)
    {
        $this->options['headers'][(string)$name] = (string)$value;

        return $this;
    }

    public function setField($name, $value)
    {
        $this->fields[(string)$name] = (string)$value;

        return $this;
    }

    public function getOptions($method = 'GET')
    {
        $options = [
            'headers' => $this->options['headers'],
        ];

        if ('GET' == strtoupper($method)) {
            $options['query'] = array_merge($this->options['query'], $this->fields);

            return $options;
        }

        if (!isset($this->options['headers']['Content-Type'])) {
            $this->options['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        switch (strtolower($this->options['headers']['Content-Type'])) {
            case 'application/json':
                $options['body'] = json_encode($this->fields);
                break;
            default:
            case 'application/x-www-form-urlencoded':
                $options['form_params'] = http_build_query($this->fields);
                break;
        }

        return $options;
    }
}

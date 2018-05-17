<?php
namespace WebwinkelKeur\Client;

abstract class RequestAbstract implements RequestInterface
{
    protected $options = [
        'http_errors' => 'false',
        'headers' => [],
        'query' => [],
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
            'query' => [],
        ];

        if ('GET' == strtoupper($method)) {
            $options['query'] = array_merge($this->options['query'], $this->fields);

            return $options;
        }

        if (!isset($this->options['headers']['Content-Type'])) {
            $this->options['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $contentType = strtolower($this->options['headers']['Content-Type']);

        if (strpos($contentType, 'application/x-www-form-urlencoded') === 0) {
            $options['form_params'] = $this->fields;

        } elseif (strpos($contentType, 'application/json') === 0) {
            $options['json'] = $this->fields;
        }

        return $options;
    }
}

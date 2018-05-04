<?php
namespace WebwinkelKeur\Client;

interface RequestInterface
{
    /**
     * Sets a request header field
     *
     * @param string $name  Name of the header field
     * @param string $value Value to set
     *
     * @return self
     */
    public function setHeader($name, $value);

    /**
     * Sets a payload field
     *
     * The fields should be encoded depending on the method and content type
     * of the request.
     *
     * @param string $name  Name of field
     * @param string $value
     *
     * @return self
     */
    public function setField($name, $value);

    /**
     * Validates the request
     *
     * @return bool
     */
    public function validate();

    /**
     * Returns an associative array of options to pass to Guzzle. Header
     * fields (e.g., Content-Type) should be taken into account when building
     * the options array.
     *
     * @param string $method HTTP method
     *
     * @return array
     */
    public function getOptions($method = 'GET');
}

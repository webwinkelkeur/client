<?php
namespace WebwinkelKeur;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use WebwinkelKeur\Client\Exception;
use WebwinkelKeur\Client\Request\Invitation;
use WebwinkelKeur\Client\RequestInterface;

class Client
{
    const USER_AGENT = 'WebwinkelKeur::Client/1.0';

    protected $_headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'User-Agent' => self::USER_AGENT,
    ];
    protected $_endpoint = 'https://dashboard.webwinkelkeur.nl/api/1.0/';
    protected $_id = '';
    protected $_code = '';
    protected $_guzzleClient = null;

    public function __construct($id, $code)
    {
        $this->setID($id);
        $this->setCode($code);
    }

    public function setEndpoint($URL)
    {
        $this->_endpoint = (string)$URL;

        return $this;
    }

    public function setID($id)
    {
        $this->_id = (string)$id;

        return $this;
    }

    public function setCode($code)
    {
        $this->_code = (string)$code;

        return $this;
    }

    public function getGuzzleClient()
    {
        if (!$this->_guzzleClient) {
            $this->_guzzleClient = new GuzzleClient(['base_uri' => $this->_endpoint]);
        }

        return $this->_guzzleClient;
    }

    public function sendInvitation(Invitation $request)
    {
        $result = $this->sendRequest('POST', 'invitations.json', $request);

        return ('success' == $result->status);
    }

    public function sendRequest($method, $URL, RequestInterface $request)
    {
        echo json_encode($request, JSON_PRETTY_PRINT);

        if (!$request->validate()) {
            throw new Exception\ValidationFailed();
        }

        $options = $request->getOptions($method);
        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] = array_merge($options['query'], ['id' => $this->_id, 'code' => $this->_code]);

        try {
            // FIXME here
            /** @var Response $response */
//            $response = $this->getGuzzleClient()->request($method, $URL, $options);
            var_export($options); return json_decode(json_encode(['status' => 'success', 'message' => 'yay']));

        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() < 200 or 299 < $response->getStatusCode()) {
            throw new Exception($result->message ?? $response->getReasonPhrase());
        }

        switch (strtolower($response->getHeader('Content-Type'))) {
            case 'application/json':
                $result = json_decode($response->getBody()->getContents());
                break;
            default:
                $result = $response->getBody()->getContents();
                break;
        }

        return $result;
    }
}

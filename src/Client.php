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

    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'User-Agent' => self::USER_AGENT,
    ];
    protected $endpoint = 'https://dashboard.webwinkelkeur.nl/api/1.0/';
    protected $id = '';
    protected $code = '';
    /** @var GuzzleClient */
    protected $guzzleClient = null;

    public function __construct($id, $code)
    {
        $this->setID($id);
        $this->setCode($code);
    }

    public function setEndpoint($URL)
    {
        $this->endpoint = (string)$URL;

        return $this;
    }

    public function setID($id)
    {
        $this->id = (string)$id;

        return $this;
    }

    public function setCode($code)
    {
        $this->code = (string)$code;

        return $this;
    }

    public function setGuzzleClient(GuzzleClient $client)
    {
        $this->guzzleClient = $client;

        return $this;
    }

    public function getGuzzleClient()
    {
        if (!$this->guzzleClient) {
            $this->setGuzzleClient(new GuzzleClient(['base_uri' => $this->endpoint]));
        }

        return $this->guzzleClient;
    }

    public function sendInvitation(Invitation $request)
    {
        $result = $this->sendRequest('POST', 'invitations.json', $request);

        if (!isset($result->status) || $result->status != 'success') {
            throw new Exception\OperationFailed(isset($result->message) ? $result->message : '');
        }

        return true;
    }

    public function sendRequest($method, $URL, RequestInterface $request)
    {
        if (!$request->validate()) {
            throw new Exception\ValidationFailed();
        }

        $options = $request->getOptions($method);
        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] = array_merge($options['query'], ['id' => $this->id, 'code' => $this->code]);

        try {
            /** @var Response $response */
            $response = $this->getGuzzleClient()->request($method, $URL, $options);

        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() < 200 || 299 < $response->getStatusCode()) {
            throw new Exception($response->getReasonPhrase());
        }

        foreach ($response->getHeader('Content-Type') as $contentType) {
            if (strpos(strtolower($contentType), 'application/json') === 0) {
                return json_decode($response->getBody()->getContents());
            }
        }

        return $response->getBody()->getContents();
    }
}

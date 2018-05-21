<?php
namespace WebwinkelKeur;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as Psr7Response;
use WebwinkelKeur\Client\Exception;
use WebwinkelKeur\Client\Request\Blank;
use WebwinkelKeur\Client\Request\Invitation;
use WebwinkelKeur\Client\RequestInterface;
use WebwinkelKeur\Client\Response;

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

    /**
     * @param string $id
     * @param string $code
     */
    public function __construct($id, $code)
    {
        $this->setID($id);
        $this->setCode($code);
    }

    /**
     * @param string $URL The URL of the API endpoint
     *
     * @return $this
     */
    public function setEndpoint($URL)
    {
        $this->endpoint = (string)$URL;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setID($id)
    {
        $this->id = (string)$id;

        return $this;
    }

    /**
     * @param string $code Authorization code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = (string)$code;

        return $this;
    }

    /**
     * @param GuzzleClient $client
     *
     * @return $this
     */
    public function setGuzzleClient(GuzzleClient $client)
    {
        $this->guzzleClient = $client;

        return $this;
    }

    /**
     * @return GuzzleClient
     */
    public function getGuzzleClient()
    {
        if (!$this->guzzleClient) {
            $this->setGuzzleClient(new GuzzleClient(['base_uri' => $this->endpoint]));
        }

        return $this->guzzleClient;
    }

    /**
     * @param Invitation $request
     */
    public function sendInvitation(Invitation $request)
    {
        $this->sendRequest('POST', 'invitations.json', $request);
    }

    /**
     * @return Response\SentInvitation[]
     */
    public function getSentInvitations()
    {
        $sentInvitations = [];

        do {
            $request = new Blank();
            $request
                ->setField('limit', 100)
                ->setField('offset', count($sentInvitations));

            $result = $this->sendRequest('GET', 'invitations.json', $request);

            foreach ($result->invitations as $invitationData) {
                $sentInvitations[] = new Response\SentInvitation($invitationData);
            }

        } while (count($sentInvitations) < $result->total);

        return $sentInvitations;
    }

    /**
     * @return Response\Review[]
     */
    public function getReviews()
    {
        $reviews = [];

        do {
            $request = new Blank();
            $request
                ->setField('limit', 100)
                ->setField('offset', count($reviews));

            $result = $this->sendRequest('GET', 'ratings.json', $request);

            foreach ($result->ratings as $reviewData) {
                $reviews[] = new Response\Review($reviewData);
            }

        } while (count($reviews) < $result->total);

        return $reviews;
    }

    /**
     * @return Response\ReviewsSummary
     */
    public function getReviewsSummary()
    {
        $result = $this->sendRequest('GET', 'ratings_summary.json');

        return new Response\ReviewsSummary($result->data);
    }

    /**
     * @param string                $method  Method of the request
     * @param string                $URL     URL to send the request to
     * @param RequestInterface|null $request Request
     *
     * @return mixed
     *
     * @throws Exception
     * @throws Exception\OperationFailed
     * @throws Exception\ValidationFailed
     */
    public function sendRequest($method, $URL, RequestInterface $request = null)
    {
        if (!$request) {
            $request = new Blank();
        }

        if (!$request->validate()) {
            throw new Exception\ValidationFailed();
        }

        $options = $request->getOptions($method);
        if (!isset($options['query'])) {
            $options['query'] = [];
        }
        $options['query'] = array_merge($options['query'], ['id' => $this->id, 'code' => $this->code]);

        try {
            /** @var Psr7Response $response */
            $response = $this->getGuzzleClient()->request($method, $URL, $options);

        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() < 200 || 299 < $response->getStatusCode()) {
            throw new Exception($response->getReasonPhrase());
        }

        foreach ($response->getHeader('Content-Type') as $contentType) {
            if (strpos(strtolower($contentType), 'application/json') !== 0) {
                continue;
            }

            $result = json_decode($response->getBody()->getContents());

            if (!isset($result->status) || $result->status != 'success') {
                throw new Exception\OperationFailed(isset($result->message) ? $result->message : '');
            }

            return $result;
        }

        return $response->getBody()->getContents();
    }
}

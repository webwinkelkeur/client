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
    protected $richSnippetURL = 'https://www.webwinkelkeur.nl/shop_rich_snippet.php';
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
     * @return \Generator|Response\SentInvitation[]
     */
    public function getSentInvitations()
    {
        $sentInvitationsCount = 0;

        do {
            $request = new Blank();
            $request
                ->setField('limit', 100)
                ->setField('offset', $sentInvitationsCount);

            $result = $this->sendRequest('GET', 'invitations.json', $request);

            foreach ($result->invitations as $invitationData) {
                $sentInvitationsCount++;

                yield new Response\SentInvitation($invitationData);
            }

        } while ($sentInvitationsCount < $result->total);
    }

    /**
     * @return \Generator|Response\Review[]
     */
    public function getReviews()
    {
        $reviewsCount = 0;

        do {
            $request = new Blank();
            $request
                ->setField('limit', 100)
                ->setField('offset', $reviewsCount);

            $result = $this->sendRequest('GET', 'ratings.json', $request);

            foreach ($result->ratings as $reviewData) {
                $reviewsCount++;
                 yield new Response\Review($reviewData);
            }

        } while ($reviewsCount < $result->total);
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
     * @return Response\Webshop
     */
    public function getWebshop()
    {
        $result = $this->sendRequest('GET', 'webshop.json');

        return new Response\Webshop($result->data);
    }

    /**
     * @return string Rich snippet
     *
     * @throws Exception
     * @throws Exception\OperationFailed
     */
    public function getRichSnippet()
    {
        try {
            /** @var Psr7Response $response */
            $response = $this->getGuzzleClient()->get($this->richSnippetURL, ['query' => ['id' => $this->id]]);

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

            if (!isset($result->result) || $result->result != 'ok') {
                throw new Exception\OperationFailed(isset($result->message) ? $result->message : '');
            }

            return $result->content;
        }

        throw new Exception\OperationFailed('Unexpected response received');
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

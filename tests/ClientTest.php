<?php
declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use WebwinkelKeur\Client;
use WebwinkelKeur\Client\Response as ClientResponse;

/**
 * Class ClientTest
 *
 * @see http://docs.guzzlephp.org/en/stable/testing.html
 */
final class ClientTest extends TestCase
{
    /** @var MockHandler */
    protected $mockHandler = null;
    /** @var Client */
    protected $client = null;

    public function setUp()
    {
        $this->mockHandler = new MockHandler();

        $this->client = new Client('id', 'code');
        $this->client->setGuzzleClient(new GuzzleClient(['handler' => HandlerStack::create($this->mockHandler)]));
    }

    private function addMockJsonResponse($json)
    {
        $this->mockHandler->append(new Response(200, ['Content-Type' => 'application/json; encoding=UTF-8'], $json));

        return $this;
    }

    public function testSendInvitationRequest()
    {
        $invitation = new Client\Request\Invitation();
        $invitation
            ->setEmailAddress('john.doe@example.org')
            ->setOrderNumber('101');

        $responses = [
            ['status' => 'success', 'message' => 'Invite successfully added to queue!'],
            ['status' => 'success', 'message' => 'Invitation already sent for this order.'],
            ['status' => 'error', 'message' => 'Unexpected error while processing.'],
        ];

        foreach ($responses as $response) {
            $this->addMockJsonResponse(json_encode($response));
        }

        $this->client->sendInvitation($invitation);
        $this->client->sendInvitation($invitation);
        $this->expectException(Client\Exception\OperationFailed::class);
        $this->client->sendInvitation($invitation);
    }

    public function testFailedRequest()
    {
        $invitation = new Client\Request\Invitation();
        $invitation
            ->setEmailAddress('john.doe@example.org')
            ->setOrderNumber('101');

        $this->mockHandler->append(new Response(200, ['Content-Type' => 'application/json; encoding=UTF-8'], json_encode(['status' => 'success'])));
        $this->client->sendInvitation($invitation);

        $this->mockHandler->append(new RequestException("Error Communicating with Server", new Request('GET', 'test')));
        $this->expectException(Client\Exception::class);
        $this->client->sendInvitation($invitation);
    }

    public function testGetSentInvitations()
    {
        $this->addMockJsonResponse('
        {
            "status": "success",
            "message": "Invitations successfully retrieved!",
            "total": 2,
            "invitations": [
                {
                    "email": "john.doe@example.com",
                    "order": "3003",
                    "delay": 2,
                    "datetimes": {
                        "created": "2018-05-10 17:55:24",
                        "scheduled": "2018-05-13 17:55:24",
                        "sent": "2018-05-16 18:01:19"
                    }
                },
                {
                    "email": "jane.doe@example.net",
                    "order": "5005",
                    "delay": 3,
                    "datetimes": {
                        "created": "2018-05-09 09:43:44",
                        "scheduled": "2018-05-12 09:43:44",
                        "sent": "2018-05-15 09:46:12"
                    }
                }
            ]
        }');

        foreach ($this->client->getSentInvitations() as $invitation) {
            $this->assertInstanceOf(ClientResponse\SentInvitation::class, $invitation);
            $this->assertInstanceOf(\DateTimeImmutable::class, $invitation->getCreatedAt());
            foreach ([3003 => 'john.doe@example.com', 5005 => 'jane.doe@example.net'] as $orderNumber => $email) {
                if ($invitation->getOrderNumber() == $orderNumber) {
                    $this->assertEquals($email, $invitation->getEmail());
                }
            }
        }
    }

    public function testGetReviews()
    {
        $this->addMockJsonResponse('
        {
            "status": "success",
            "message": "Ratings successfully retrieved!",
            "total": 2,
            "ratings": [
                {
                    "name": "Frank",
                    "email": "frank@example.org",
                    "rating": 4,
                    "ratings": {
                        "shippingtime": 4,
                        "customerservice": 3,
                        "pricequality": 4,
                        "aftersale": 5
                    },
                    "comment": "Awesome service!",
                    "date": "2014-10-05",
                    "read": true,
                    "quarantine": false
                },
                {
                    "name": "Pieter",
                    "email": "pieter@example.net",
                    "rating": 5,
                    "ratings": {
                        "shippingtime": 0,
                        "customerservice": 0,
                        "pricequality": 0,
                        "aftersale": 0
                    },
                    "comment": "Bedankt voor de uiterst goede after-sales!",
                    "date": "2010-07-20",
                    "read": true,
                    "quarantine": false
                }
            ]
        }');

        $reviews = $this->client->getReviews();
        $this->assertEquals(2, count($reviews));
        $this->assertEquals('Pieter', $reviews[1]->getName());

        $this->addMockJsonResponse('
        {
            "status": "success",
            "message": "No ratings found.",
            "total": 0,
            "ratings": []
        }');

        $noResults = $this->client->getReviews();

        $this->assertEmpty($noResults);
    }
}

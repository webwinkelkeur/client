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
    protected $mockHandler = null;
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
    }

    public function testSendInvitationRequest()
    {
        $invitation = new Client\Request\Invitation();
        $invitation
            ->setEmailAddress('john.doe@example.org')
            ->setOrderNumber('101');

        $this->assertTrue($this->client->sendInvitation($invitation));
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
}

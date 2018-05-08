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

/**
 * Class ClientTest
 *
 * @see http://docs.guzzlephp.org/en/stable/testing.html
 */
final class ClientTest extends TestCase
{
    protected $client = null;

    public function setUp()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json; encoding=UTF-8'], json_encode(['status' => 'success'])),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);

        $this->client = new Client('id', 'code');
        $this->client->setGuzzleClient(new GuzzleClient(['handler' => $handler]));
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

        $this->client->sendInvitation($invitation);

        $this->expectException(Client\Exception::class);
        $this->client->sendInvitation($invitation);
    }
}

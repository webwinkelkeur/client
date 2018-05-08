<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use WebwinkelKeur\Client\Request\Invitation;
use WebwinkelKeur\Client\Exception;

final class InvitationTest extends TestCase
{
    public function testMustHaveEmailAddress()
    {
        $invitation = new Invitation();
        $this->assertFalse($invitation->validate());
    }

    public function testNeedsAnOrderNumber()
    {
        $invitation = new Invitation();

        $this->expectException(Exception\ValidationFailed::class);
        $invitation->setOrderNumber(false);

        $this->expectException(Exception\ValidationFailed::class);
        $invitation->setOrderNumber('');
    }

    public function negativeNumbers()
    {
        return [
            [-234.43],
            [-22],
            ['-22'],
            ['-0.000000000000001'],
            [-0.000000001],
            [-0x1],
        ];
    }

    public function NaNs()
    {
        return [
            'string' => ['string value'],
            'bool' => [true],
            'null' => [null],
        ];
    }

    /**
     * @dataProvider negativeNumbers
     */
    public function testOrderTotalMustBePositiveNumber($negativeNumber)
    {
        $invitation = new Invitation();

        $this->expectException(Exception\ValidationFailed::class);
        $invitation->setOrderTotal($negativeNumber);
    }

    /**
     * @dataProvider NaNs
     */
    public function testOrderTotalMustBeNumeric($NaN)
    {
        $invitation = new Invitation();

        $this->expectException(Exception\ValidationFailed::class);
        $invitation->setOrderTotal($NaN);
    }

    public function validEmailAddresses()
    {
        return [
            'domain with tld' => ['john.doe@example.org'],
            'locally resolvable hostname' => ['john.doe@example-no-tld'],
            'ip address' => ['root@[127.0.0.1]'],
        ];
    }

    /**
     * @dataProvider validEmailAddresses
     */
    public function testMustHaveValidEmailAddress($emailAddress)
    {
        $invitation = new Invitation();
        $invitation->setEmailAddress($emailAddress);
        $this->assertTrue($invitation->validate());
    }

    public function testMustFailOnInvalidEmailAddress()
    {
        $invitation = new Invitation();
        $this->expectException(Exception\ValidationFailed::class);
        $invitation->setEmailAddress('string without "at" symbol');
    }
}

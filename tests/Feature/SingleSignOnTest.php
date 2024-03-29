<?php
namespace Tests\Feature;

use MatthewJensen\LaravelDiscourse\Facades\DiscourseAuth as Discourse;
use Orchestra\Testbench\TestCase;

class SingleSignOnTest extends TestCase
{

    const SECRET = 'my_sso_secret';
    const PAYLOAD = 'bm9uY2U9ZTE4YmRiYTgxOTdhZGUwOTZkOTY0NTdkNDg2NzViYjkmcmV0dXJu%0AX3Nzb191cmw9aHR0cCUzQSUyRiUyRmRpc2NvdXJzZS5iZWF1Y2FsLmNvbSUy%0ARnNlc3Npb24lMkZzc29fbG9naW4%3D%0A';
    const SIGNATURE = '112119cead5be8305852bdd34536d6fb71ad6fef240be1c486412bcf078f41dd';
    const NONCE = 'e18bdba8197ade096d96457d48675bb9';

    /**
     * @var Discourse
     */
    protected $sso;

    public function setUp() : void
    {
        parent::setUp();
        Discourse::setSecret(self::SECRET);
        \Auth::routes();
    }
    protected function getPackageProviders($app)
    {
        return ['MatthewJensen\LaravelDiscourse\DiscourseServiceProvider'];
    }

    public function testController() {
        $response = $this->get(route('sso.logout'));
        $response->assertRedirect(route('login'));
    }
    public function testInOut()
    {
        $this->assertTrue(
            Discourse::validatePayload(self::PAYLOAD, self::SIGNATURE)
        );

        $userId = 1234;
        $userEmail = 'sso@example.com';
        $response = Discourse::getSignInString(
            Discourse::getNonce(self::PAYLOAD), $userId, $userEmail
        );
        $expected = 'sso=bm9uY2U9ZTE4YmRiYTgxOTdhZGUwOTZkOTY0'
            . 'NTdkNDg2NzViYjkmZXh0ZXJuYWxfaWQ9MTIzNCZlbWFpbD1zc2'
            . '8lNDBleGFtcGxlLmNvbQ%3D%3D&sig=9db5456c6d21b8bad96'
            . 'a9071edfd0fd87160f7b71687dbbed2050d4c7750b643';
        $this->assertEquals($expected, $response);
    }

    public function testNonceGood()
    {
        $payload = base64_encode('nonce=1111');
        $this->assertEquals(1111, Discourse::getNonce($payload));

        $payload = base64_encode('nonce=2222&asdf=true');
        $this->assertEquals(2222, Discourse::getNonce($payload));
    }

    /**
     * @expectedException \MatthewJensen\LaravelDiscourse\Exceptions\PayloadException
     */
    public function testNonceBad1()
    {
        $payload = base64_encode('nonc=1111');
        Discourse::getNonce($payload);
    }

    /**
     * @expectedException \MatthewJensen\LaravelDiscourse\Exceptions\PayloadException
     */
    public function testNonceBad2()
    {
        Discourse::getNonce('junk');
    }

    public function testExtraParametersPlayNice()
    {
        $userId = 1234;
        $userEmail = 'sso@example.com';
        $extraParams = array(
            'nonce'       => 'junk',
            'external_id' => 'junk',
            'email'       => 'junk',
            'only_me'     => 'gets_through',
        );
        $response = Discourse::getSignInString(
            Discourse::getNonce(self::PAYLOAD), $userId, $userEmail, $extraParams
        );
        parse_str($response, $response);
        $parts = array();
        parse_str(base64_decode($response['sso']), $parts);
        $this->assertEquals($userId, $parts['external_id']);
        $this->assertEquals($userEmail, $parts['email']);
        $this->assertEquals(self::NONCE, $parts['nonce']);
        $this->assertEquals('gets_through', $parts['only_me']);
    }
}

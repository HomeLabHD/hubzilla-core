<?php
/**
 * tests function from include/network.php
 *
 * @package test.util
 */

class NetworkTest extends Zotlabs\Tests\Unit\UnitTestCase {

	/**
	 * @dataProvider localUrlTestProvider
	 */
	public function testIsLocalURL($url, $expected) {
		$this->assertEquals($expected, is_local_url($url));
	}

	public static function localUrlTestProvider() : array {
		return [
			[ '/some/path', true ],
			[ 'https://hubzilla.test/some/path', true ],
			[ 'https://other.site/some/path', false ],
		];
	}

	/**
	 * Test the validate_email function.
	 *
	 * @dataProvider validate_email_provider
	 */
	public function test_validate_email(string $email, bool $expected) : void {
		$this->assertEquals($expected, validate_email($email));
	}

	/**
	 * Test that the validate_email function is disabled when configured to.
	 *
	 * @dataProvider validate_email_provider
	 */
	public function test_disable_validate_email(string $email) : void {
		\Zotlabs\Lib\Config::Set('system', 'disable_email_validation', true);
		$this->assertTrue(validate_email($email));
	}

	public static function validate_email_provider() : array {
		return [
			// First some invalid email addresses
			['', false],
			['not_an_email', false],
			['@not_an_email', false],
			['not@an@email', false],
			['not@an@email.com', false],

			// then test valid addresses too
			['test@example.com', true],

			// Should also work with international domains
			['some.email@dømain.net', true],

			// Should also work with the new top-level domains
			['some.email@example.cancerresearch', true],

			// And internationalized TLD's
			['some.email@example.شبكة', true],

			// Allow plus/minus addressing
			['address+tag@example.com', true],
			['address-tag@example.com', true],
		];
	}

	/**
	 * Test the unparse_url function.
	 *
	 */
	public function test_unparse_url_full()
	{
		$parsed_url = [
			'scheme' => 'https',
			'host' => 'www.example.com',
			'port' => '8080',
			'user' => 'username',
			'pass' => 'password',
			'path' => '/path',
			'query' => 'param=value',
			'fragment' => 'section'
		];

		$expected = 'https://username:password@www.example.com:8080/path?param=value#section';
		$this->assertEquals($expected, unparse_url($parsed_url));
	}

	public function test_unparse_url_partial()
	{
		$parsed_url = [
			'scheme' => 'http',
			'host' => 'example.com',
			'path' => '/index.php'
		];

		$expected = 'http://example.com/index.php';
		$this->assertEquals($expected, unparse_url($parsed_url));
	}

	public function test_unparse_url_custom()
	{
		$parsed_url = [
			'scheme' => 'https',
			'host' => 'www.example.com',
			'port' => '443',
			'path' => '/api'
		];

		$parts = ['scheme', 'host'];
		$expected = 'https://www.example.com';
		$this->assertEquals($expected, unparse_url($parsed_url, $parts));
	}

	public function test_unparse_url_empty()
	{
		$this->assertEquals('', unparse_url([]));
	}


	/**
	 * Test that the parse_webbie function.
	 *
	 * @dataProvider parse_webbie_provider
	 */
	public function test_parse_webbie(string $webbie, array|false $expected) : void {
		$this->assertEquals($expected, parse_webbie($webbie));
	}

	public static function parse_webbie_provider() : array {
		return [
			// test valid webfinger address
			['test@example.net', ['host' => 'example.net', 'resource' => urlencode('acct:test@example.net')]],

			// test valid webfinger address with scheme
			['acct:test@example.net', ['host' => 'example.net', 'resource' => urlencode('acct:test@example.net')]],

			// test address with leading @
			['@test@example.net', ['host' => 'example.net', 'resource' => urlencode('acct:test@example.net')]],

			// test address with missing user
			['@example.net', false],

			// test URL
			['https://example.net/channel/test', ['host' => 'example.net', 'resource' => urlencode('https://example.net/channel/test')]],

			// test unsupported URL
			['ftp://example.net/channel/test', false],
		];
	}

}

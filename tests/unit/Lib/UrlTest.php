<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Mario Vavti <mario@mariovavti.com>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;
use Zotlabs\Lib\Url;
use phpmock\phpunit\PHPMock;


class UrlTest extends UnitTestCase {

	use PHPMock;

	/**
	 * Test Url::zid()
	 *
	 * @dataProvider zid_test_provider
	 */
	public function test_zid(string $url, string $addr, string $expected) : void {
		// Mock get_my_url()
        $getMyUrl = $this->getFunctionMock('Zotlabs\Lib', 'get_my_url');
        $getMyUrl->expects($this->atMost(1))
                 ->willReturn('https://example.org/channel/visitor');

        $getMyAdr = $this->getFunctionMock('Zotlabs\Lib', 'get_my_address');
        $getMyAdr->expects($this->atMost(1))
                 ->willReturn('visitor@example.org');

		$this->assertEquals(urldecode(Url::zid($url, $addr)), $expected);
	}

	public static function zid_test_provider() : array {
		return [
			// URL without params
			['https://example.net/channel/test', 'visitor@example.org', 'https://example.net/channel/test?zid=visitor@example.org'],
			// URL with args
			['https://example.net/channel/test?t=test', 'visitor@example.org', 'https://example.net/channel/test?t=test&zid=visitor@example.org'],
			// URL with fragment
			['https://example.net/channel/test#fragment', 'visitor@example.org', 'https://example.net/channel/test?zid=visitor@example.org#fragment'],
			// URL with args and fragment
			['https://example.net/channel/test?t=test#fragment', 'visitor@example.org', 'https://example.net/channel/test?t=test&zid=visitor@example.org#fragment'],
			// URL with zid
			['https://example.net/channel/test?zid=visitor@example.org', 'visitor@example.org', 'https://example.net/channel/test?zid=visitor@example.org'],
			// No addr provided
			['https://example.net/channel/test', '', 'https://example.net/channel/test?zid=visitor@example.org'],
			// Same host, no addr
			['https://example.org/channel/test', '', 'https://example.org/channel/test'],
		];
	}


	/**
	 * Test Url::unparse()
	 *
	 */
	public function test_unparse_full()
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
		$this->assertEquals($expected, Url::unparse($parsed_url));
	}

	public function test_unparse_partial()
	{
		$parsed_url = [
			'scheme' => 'http',
			'host' => 'example.com',
			'path' => '/index.php'
		];

		$expected = 'http://example.com/index.php';
		$this->assertEquals($expected, Url::unparse($parsed_url));
	}

	public function test_unparse_custom()
	{
		$parsed_url = [
			'scheme' => 'https',
			'host' => 'www.example.com',
			'port' => '443',
			'path' => '/api'
		];

		$parts = ['scheme', 'host'];
		$expected = 'https://www.example.com';
		$this->assertEquals($expected, Url::unparse($parsed_url, $parts));
	}

	public function test_unparse_empty()
	{
		$this->assertEquals('', Url::unparse([]));
	}


}

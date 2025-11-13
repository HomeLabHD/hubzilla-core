<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Lib;

use PHPUnit\Framework\Attributes\TestWith;
use Zotlabs\Lib\Zotfinger;
use Zotlabs\Tests\Unit\UnitTestCase;

use phpmock\phpunit\PHPMock;

class ZotfingerTest extends UnitTestCase
{
	// Import PHPMock methods into this class
	use PHPMock;

	#[TestWith([null])]
	#[TestWith([false])]
	#[TestWith([''])]
	public function testShouldReturnFalseOnNoResource($resource): void
	{
		$this->assertFalse(Zotfinger::exec($resource));
	}

	public function testShouldReturnFalseIfResourceNotFound(): void
	{
		//
		// Mock z_post_url to prevent it fram connecting to an actual remote site,
		// and so that we can control what it should return.
		//
		// Here we simulate a request that return 404 Not Found.
		//
		$mock = $this->getFunctionMock('Zotlabs\Lib', 'z_post_url')
			   ->expects($this->once())
			   ->willReturn([
					'return_code' => 404,
					'success' => false,
			   ]);

		$this->assertFalse(Zotfinger::exec('https://example.test/some-resource'));
	}

	public function testShouldReturnBodyIfResourceFound(): void
	{
		//
		// Mock z_post_url to prevent it fram connecting to an actual remote site,
		// and so that we can control what it should return.
		//
		// Here we simulate a request returning 200 OK and some data
		//
		$mock = $this->getFunctionMock('Zotlabs\Lib', 'z_post_url')
			   ->expects($this->once())
			   ->willReturn([
					'return_code' => 200,
					'success' => true,
					'body' => '{"subject":"acct:user@example.test"}',
			   ]);

		//
		// Initialize some $_SERVER superglobal values needed by HTTPSig:
		//
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = 'some_uri';
		$_SERVER['CONTENT_TYPE'] = 'application/json';
		$_SERVER['CONTENT_LENGTH'] = 42;

		$result = Zotfinger::exec('https://example.test/some-resource');

		//
		// Verify that the json payload has been decoded
		//
		$this->assertIsArray($result['data']);
		$this->assertArrayHasKey('subject', $result['data']);
		$this->assertEquals('acct:user@example.test', $result['data']['subject']);
	}

	public function testThatRequestFromChannelIsSigned(): void
	{
		//
		// Mock z_post_url to prevent it fram connecting to an actual remote site,
		// and so that we can control what it should return.
		//
		// Here we simulate a request returning 200 OK and some data
		//
		$mock = $this->getFunctionMock('Zotlabs\Lib', 'z_post_url')
				->expects($this->once())
				->willReturn([
					'return_code' => 200,
					'success' => true,
					'body' => '{"subject":"acct:user@example.test"}',
				]);

		//
		// Initialize some $_SERVER superglobal values needed by HTTPSig:
		//
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = 'some_uri';
		$_SERVER['CONTENT_TYPE'] = 'application/json';
		$_SERVER['CONTENT_LENGTH'] = 42;


		$result = create_identity([
			'account_id' => $this->fixtures['account'][0]['account_id'],
			'name' => 'Test User',
			'nickname' => 'testuser',
		]);

		$this->assertTrue($result['success']);

		$testresult = Zotfinger::exec('https://example.test/some-resource', $result['channel']);

		//
		// Verify that the json payload has been decoded
		//
		$this->assertIsArray($testresult['data']);
		$this->assertArrayHasKey('subject', $testresult['data']);
		$this->assertEquals('acct:user@example.test', $testresult['data']['subject']);
	}
}

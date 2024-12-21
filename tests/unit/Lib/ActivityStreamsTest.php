<?php
/**
 * Unit tests for Zotlabs\Lib\ActivityStreams.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Lib;

use phpmock\phpunit\PHPMock;
use Zotlabs\Lib\ActivityStreams;
use Zotlabs\Tests\Unit\UnitTestCase;

class ActivityStreamsTest extends UnitTestCase {

	// Import PHPMock methods into this class
	use PHPMock;

	/**
	 * Test parsing an announce activity of a like from a remote server of
	 * a note from a third server.
	 *
	 * Also test that we fetch the referenced objects when the received
	 * activity is parsed,
	 */
	public function test_parse_announce_of_like(): void {
		$payload = <<<JSON
			{
			  "actor": "https://lemmy.test/c/technology",
			  "to": [
				"https://www.w3.org/ns/activitystreams#Public"
			  ],
			  "object": {
				"id": "https://lemmy.test/activities/like/e6e38c8b-beee-406f-9523-9da7ec97a823",
				"actor": "https://lemmy.test/u/SomePerson",
				"object": "https://somesite.test/post/1197552",
				"type": "Like",
				"audience": "https://lemmy.test/c/technology"
			  },
			  "cc": [
				"https://lemmy.test/c/technology/followers"
			  ],
			  "type": "Announce",
			  "id": "https://lemmy.test/activities/announce/like/9e583a54-e4e0-4436-9726-975a14f923ed"
			}
			JSON;

		//
		// Mock z_fetch_url to prevent us from spamming real servers during test runs
		//
		// We just create some sample ActivityStreams objects to return for the various
		// URL's to make it a somewhat realistic test. Each object will have it's URL as
		// it's id and only specify the object type as laid out in the $urlmap below.

		$urlmap = [
			'https://lemmy.test/c/technology' => [ 'type' => 'Group' ],
			'https://lemmy.test/u/SomePerson' => [ 'type' => 'Person' ],
			'https://somesite.test/post/1197552' => [ 'type' => 'Note' ],
		];

		$z_fetch_url_stub = $this->getFunctionMock('Zotlabs\Lib', 'z_fetch_url');
		$z_fetch_url_stub
			->expects($this->any())
			->willReturnCallback(function ($url) use ($urlmap) {
				if (isset($urlmap[$url])) {
					$body = json_encode(
						array_merge([ 'id' => $url ], $urlmap[$url]),
						JSON_FORCE_OBJECT,
					);

					return [
						'success' => true,
						'body' => $body,
					];
				} else {
					// We should perhaps throw an error here to fail the test,
					// as we're receiving an unexpected URL.
					return [
						'success' => false,
					];
				}
			});

		// Make sure we have a sys channel before we start
		create_sys_channel();

		$as = new ActivityStreams($payload);

		$this->assertTrue($as->valid);

		$this->assertEquals(
			'https://lemmy.test/activities/announce/like/9e583a54-e4e0-4436-9726-975a14f923ed',
			$as->id
		);

		$this->assertEquals('Announce', $as->type);

		$this->assertIsArray($as->actor);
		$this->assertArrayHasKey('id', $as->actor);
		$this->assertEquals('https://lemmy.test/c/technology', $as->actor['id']);
		$this->assertArrayHasKey('type', $as->actor);
		$this->assertEquals('Group', $as->actor['type']);

		$this->assertIsArray($as->recips);
		$this->assertContains('https://www.w3.org/ns/activitystreams#Public', $as->recips);
		$this->assertContains('https://lemmy.test/c/technology/followers', $as->recips);
		$this->assertContains('https://lemmy.test/c/technology', $as->recips);

		$this->assertIsArray($as->obj);
		$this->assertArrayHasKey('id', $as->obj);
		$this->assertEquals(
			'https://lemmy.test/activities/like/e6e38c8b-beee-406f-9523-9da7ec97a823',
			$as->obj['id']
		);
		$this->assertArrayHasKey('type', $as->obj);
		$this->assertEquals('Like', $as->obj['type']);
		$this->assertArrayHasKey('object', $as->obj);

		$this->assertIsArray($as->obj['object']);

		$this->assertArrayHasKey('id', $as->obj['object']);
		$this->assertEquals('https://somesite.test/post/1197552', $as->obj['object']['id']);

		$this->assertArrayHasKey('type', $as->obj['object']);
		$this->assertEquals('Note', $as->obj['object']['type']);

		$this->assertIsArray($as->obj['actor']);
		$this->assertArrayHasKey('id', $as->obj['actor']);
		$this->assertEquals('https://lemmy.test/u/SomePerson', $as->obj['actor']['id']);
		$this->assertArrayHasKey('type', $as->obj['actor']);
		$this->assertEquals('Person', $as->obj['actor']['type']);
	}
}

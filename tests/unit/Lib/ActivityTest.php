<?php
namespace Zotlabs\Tests\Unit\Lib;

error_reporting(E_ALL);

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\Activity;
use Zotlabs\Lib\ActivityStreams;
use phpmock\phpunit\PHPMock;

class ActivityTest extends UnitTestCase {
	// Import PHPMock methods into this class
	use PHPMock;

	/**
	 * Test get a textfield from an activitystreams object
	 *
	 * @dataProvider get_textfield_provider
	 */
	public function test_get_textfield(array $src, null|string|array $expected): void {
		$this->assertEquals($expected, Activity::get_textfield($src, 'content'));
	}

	/**
	 * Dataprovider for test_get_textfield.
	 */
	public static function get_textfield_provider(): array {
		return [
			'get content field' => [
				['content' => 'Some content'],
				'Some content'
			],
			'get content from map' => [
				['contentMap' => ['en' => 'Some content']],
				['en' => 'Some content']
			],
			'get not available content' => [
				['some_field' => 'Some content'],
				null
			]
		];
	}

	public function test_get_mid_and_uuid(): void {
		$payload = <<<JSON
			{
			  "actor": "https://somesite.test/c/technology",
			  "to": [
				"https://www.w3.org/ns/activitystreams#Public"
			  ],
			  "object": {
				"id": "https://somesite.test/activities/like/e6e38c8b-beee-406f-9523-9da7ec97a823",
				"uuid": "e6e38c8b-beee-406f-9523-9da7ec97a823",
				"actor": "https://somesite.test/u/SomePerson",
				"object": "https://somesite.test/post/1197552",
				"type": "Like",
				"audience": "https://somesite.test/c/technology"
			  },
			  "cc": [
				"https://somesite.test/c/technology/followers"
			  ],
			  "type": "Announce",
			  "id": "https://somesite.test/activities/announce/like/9e583a54-e4e0-4436-9726-975a14f923ed",
			  "uuid": "9e583a54-e4e0-4436-9726-975a14f923ed"
			}
			JSON;

		//
		// Mock z_fetch_url to prevent us from spamming real servers during test runs
		//
		// We just create some sample ActivityStreams objects to return for the various
		// URL's to make it a somewhat realistic test. Each object will have it's URL as
		// it's id and only specify the object type as laid out in the $urlmap below.

		$urlmap = [
			'https://somesite.test/u/SomePerson' => [ 'type' => 'Person' ],
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

		$this->assertEquals('https://somesite.test/activities/announce/like/9e583a54-e4e0-4436-9726-975a14f923ed', Activity::getMessageID($as));
		$this->assertEquals('9e583a54-e4e0-4436-9726-975a14f923ed', Activity::getUUID($as));
	}

}

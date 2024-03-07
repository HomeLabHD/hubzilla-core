<?php
namespace Zotlabs\Tests\Unit\Lib;

error_reporting(E_ALL);

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\Activity;
use Zotlabs\Lib\ActivityStreams;

class ActivityTest extends UnitTestCase {
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
	private function get_textfield_provider(): array {
		return [
			'get content field' => [
				['content' => 'Some content'],
				'Some content'
			],
			'get content from map' => [
				['contentMap' => ['en' => 'Some content']],
				[
					'en' => 'Some content'
				]
			],
			'get not available content' => [
				['some_field' => 'Some content'],
				null
			]
		];
	}

}

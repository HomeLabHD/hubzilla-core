<?php
namespace Zotlabs\Tests\Unit\Lib;

use Zotlabs\Tests\Unit\UnitTestCase;

class ZotlibTest extends UnitTestCase {
	/**
	 * Test the `get_rpost_path` function.
	 *
	 * @dataProvider get_rpost_path_provider
	 */
	public function test_get_rpost_path(string $expected, string $xchan_url) : void {
		$observer = [ 'xchan_url' => $xchan_url ];

		$this->assertEquals($expected, \Zotlabs\Lib\Libzot::get_rpost_path($observer));
	}

	public static function get_rpost_path_provider() : array {
		return [
			'xchan_url without port' => [
				'https://example.com/rpost?f=',
				'https://example.com'
			],
			'xchan_url with port' => [
				'https://example.com:666/rpost?f=',
				'https://example.com:666'
			],
			'xchan_url ignores path and args' => [
				'https://example.com/rpost?f=',
				'https://example.com/path?arg1=balle'
			],
			'xchan_url with no scheme should default to https' => [
				'https://example.com/rpost?f=',
				'example.com',
			],
		];
	}
}

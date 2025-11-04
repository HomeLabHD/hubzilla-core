<?php
namespace Zotlabs\Tests\Unit\Lib;

error_reporting(E_ALL);

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\ASCache;

class ASCacheTest extends UnitTestCase {

	/**
	 * @dataProvider isCacheableProvider
	 */
	public function testIsCacheable(array $obj, bool $expected)
	{
		$result = ASCache::isCacheable($obj);
		$this->assertSame($expected, $result);
	}

	public static function isCacheableProvider(): array
	{
		return [
			'to field as:public' => [
				['to' => [ACTIVITY_PUBLIC_INBOX]],
				true
			],
			'cc field as:public' => [
				['to' => ['https://example.com/channel/test'], 'cc' => [ACTIVITY_PUBLIC_INBOX]],
				true
			],
			'fields are strings' => [
				['to' => 'https://example.com/channel/test', 'cc' => ACTIVITY_PUBLIC_INBOX],
				true
			],
			'none  as:public' => [
				['to' => ['https://example.com/channel/test'], 'cc' => ['https://example.com/channel/test/followers']],
				false
			],
			'no  addressing' => [
				['type' => ['Person'], 'url' => ['https://example.com/channel/test']],
				true
			],
		];
	}

}

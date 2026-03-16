<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

use PHPUnit\Framework\Attributes\Before;
use Zotlabs\Lib\Libzot;

class ZidTest extends UnitTestCase {

	/**
	 * Test the drop_query_params function.
	 *
	 * @dataProvider drop_query_params_provider
	 */
	public function test_drop_query_params(string $url, array $drop_params, string $expected) : void {
		$this->assertEquals(escape_tags($expected), drop_query_params($url, $drop_params));
	}

	public static function drop_query_params_provider() : array {
		return [
			// Query params with array
			['https://www.example.net/en/pro/detail/some-detail?tx_news_pi1%5Bday%5D=15&tx_news_pi1%5Bmonth%5D=3&tx_news_pi1%5Byear%5D=2026&cHash=85200a0007de8fecd4cd55199146e19c', ['zid', 'f'], 'https://www.example.net/en/pro/detail/some-detail?tx_news_pi1%5Bday%5D=15&tx_news_pi1%5Bmonth%5D=3&tx_news_pi1%5Byear%5D=2026&cHash=85200a0007de8fecd4cd55199146e19c'],
			// Query params with zid
			['https://www.example.net/channel/test?zid=test@example.net', ['zid', 'f'], 'https://www.example.net/channel/test'],
			// Query params with zid and empty f
			['https://www.example.net/channel/test?f=&zid=test@example.net', ['zid', 'f'], 'https://www.example.net/channel/test'],
			// Query params with zid and empty f encoded
			['https://www.example.net/channel/test?f=&amp;zid=test@example.net', ['zid', 'f'], 'https://www.example.net/channel/test'],
		];
	}

}

<?php
/*
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;

class CleanupBBCodeTest extends UnitTestCase {
	#[DataProvider("cleanup_bbcode_provider")]
	public function test_cleanup_bbcode(string $expected, string $input): void {
		$this->assertEquals($expected, cleanup_bbcode($input));
	}

	public static function cleanup_bbcode_provider(): array {
		return [
			'url followed by newline' => [
				"#^[url=https://example.com]https://example.com[/url]\na test link",
				"https://example.com\na test link",
			]
		];
	}
}

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
				"[url=https://example.com]https://example.com[/url]\na test link",
				"https://example.com\na test link",
			],
			'bookmarked url' => [
				"#^[url=https://example.com]https://example.com[/url] a test link",
				"#^https://example.com a test link",
			],
			'naked url followed by question mark' => [
				"Is this the link [url=https://example.com]https://example.com[/url]?",
				"Is this the link https://example.com?",
			],
			'naked url with query params' => [
				"Is this the link [url=https://example.com?arg1=42]https://example.com?arg1=42[/url]",
				"Is this the link https://example.com?arg1=42",
			],
			//
			// This scenario does not work. Not sure if it's worth the bother
			//
			// 'naked url with query params followed by question mark' => [
			// 	"Is this the link [url=https://example.com?arg1=42]https://example.com?arg1=42[/url]?",
			// 	"Is this the link https://example.com?arg1=42?",
			// ],
			//
			'naked IPv6 url with port number' => [
				"[url=https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/]https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/[/url]",
				"https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/",
			],
			'naked url with zid turn to zrl' => [
				"[zrl=https://example.com/?f=]https://example.com/?f=[/zrl]",
				"https://example.com/?f=&zid=kjhkjhkjh",
			],
			'naked IPv6 url with zid' => [
				"[zrl=https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/]https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/[/zrl]",
				"https://[2001:db8:85a3:8d3:1319:8a2e:370:7348]:443/?zid=kjbkjbkjb",
			],
		];
	}
}

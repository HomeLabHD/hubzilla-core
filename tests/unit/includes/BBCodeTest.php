<?php
/*
 * Copyright (c) 2024 Hubzilla
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Zotlabs\Tests\Unit\includes;

use Zotlabs\Tests\Unit\UnitTestCase;

class BBCodeTest extends UnitTestCase {
	/**
	 * Test converting html to BBCode.
	 *
	 * @dataProvider html2bbcode_provider
	 */
	public function test_html2bbcode(string $src, string $expected): void {
		$this->assertEquals($expected, html2bbcode($src));
	}

	private function html2bbcode_provider(): array {
		return [
			'paragraph over multiple lines' => [
				"<p>A paragraph over\nmultiple lines\nshould be unwrapped</p>",
				'A paragraph over multiple lines should be unwrapped'
			],
			'image with alt text' => [
				'<img src="https://example.com/image.jpg" alt="Alt text">',
				'[img=https://example.com/image.jpg]Alt text[/img]'
			],
			'code block' => [
				"<pre><code>some\ncode</code></pre>",
				"[code]some\ncode[/code]"
			],
			'code block with indentation' => [
				"<pre><code>some\n    indented\ncode</code></pre>",
				"[code]some\n    indented\ncode[/code]"
			],
			'paragraph with a mention and some text' => [
				'<p><span class="h-card" translate="no"><a href="https://example.org/@profile" class="u-url mention">@<span>profile</span></a></span> some content</p>',
				'[url=https://example.org/@profile]@profile[/url] some content'
			]
		];
	}
}

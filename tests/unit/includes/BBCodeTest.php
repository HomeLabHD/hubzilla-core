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
	 * Test converting BBCode to HTML
	 *
	 * @dataProvider bbcode_to_html_provider
	 */
	public function test_parsing_bbcode_to_html(string $src, string $expected): void {
		$this->assertBBCode($expected, $src);
	}

	/**
	 * Test the `[observer]` BBCode tags.
	 *
	 * @dataProvider bbcode_observer_provider
	 */
	public function test_bbcode_observer(string $src, bool $logged_in, string $lang, string $expected): void {
		if ($logged_in) {
			\App::$observer = [
				'xchan_addr' => '',
				'xchan_name' => '',
				'xchan_connurl' => '',
				'xchan_photo_l' => '',

				// port required in xchan url due to bug in get_rpost_path
				'xchan_url' => 'https://example.com:666',
			];
		} else {
			\App::$observer = null;
		}

		\App::$language = $lang;

		$this->assertBBCode($expected, $src);
	}

	/**
	 * Test parsing the `[channel]` tag.
	 */
	public function test_bbcode_channel(): void {
		$src = '[channel=1]This is only for channels[/channel][channel=0]This is for everyone else[/channel]';

		// Verify that the right part is shown to users _not_ in a channel
		\App::$channel = null;
		$this->assertBBCode('This is for everyone else', $src);

		// Now verify that the right part is shown to users _in_ a channel
		\App::$channel = [42];
		$this->assertBBCode('This is only for channels', $src);
	}

	/**
	 * Test converting html to BBCode.
	 *
	 * @dataProvider html2bbcode_provider
	 */
	public function test_html2bbcode(string $src, string $expected): void {
		$this->assertEquals($expected, html2bbcode($src));
	}

	/**
	 * Helper method to validate BBCode conversions.
	 *
	 * @param string $expected	The expected result of the conversion.
	 * @param string $src		The BBCode to be converted.
	 */
	private function assertBBCode(string $expected, string $src): void {
		// Note! We turn off trying to create oembeds, as that will trigger a
		// network request when running the test.
		$this->assertEquals($expected, bbcode($src, ['tryoembed' => false]));
	}

	/**
	 * Dataprovider for test_parsing_bbcode_to_html.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public static function bbcode_to_html_provider(): array {
		return [
			'code block' => [
				"[code]\ntestvar = \"this is a test\"\necho \"the message is \$testvar\"\n[/code]",
				'<pre><code>testvar = "this is a test"<br />echo "the message is $testvar"</code></pre>',
			],
			'code block with surroundin linebreaks \n' => [
				"some text\n[code]\ntestvar = \"this is a test\"\necho \"the message is \$testvar\"\n[/code]\nsome more text",
				'some text<br /><pre><code>testvar = "this is a test"<br />echo "the message is $testvar"</code></pre>some more text',
			],
			'list with linebreaks \n' => [
				"some text\n[list]\n[*] item1\n[*] item2\n[/list]\nsome more text",
				'some text<br /><ul class="listbullet"><li> item1<li> item2</ul>some more text'
			],
			'list with linebreaks \n in text' => [
				"some text\n[list]\n[*] item1\nsome text[*] item2\nsome text[/list]\nsome more text",
				'some text<br /><ul class="listbullet"><li> item1<br />some text<li> item2<br />some text</ul>some more text'
			],
			'list with linebreaks \r' => [
				"some text\r[list]\r[*] item1\r[*] item2\r[/list]\rsome more text",
				'some text<br /><ul class="listbullet"><li> item1<li> item2</ul>some more text'
			],
			'list with linebreaks \r in text' => [
				"some text\r[list]\r[*] item1\rsome text\r[*] item2\rsome text\r[/list]\rsome more text",
				'some text<br /><ul class="listbullet"><li> item1<br />some text<li> item2<br />some text</ul>some more text'
			],
			'list with linebreaks \r\n' => [
				"some text\r\n[list]\r\n[*] item1\r\n[*] item2\r\n[/list]\r\nsome more text",
				'some text<br /><ul class="listbullet"><li> item1<li> item2</ul>some more text'
			],
			'list with linebreaks \r\n in text' => [
				"some text\r\n[list]\r\n[*] item1\r\nsome text[*] item2\r\nsome text[/list]\r\nsome more text",
				'some text<br /><ul class="listbullet"><li> item1<br />some text<li> item2<br />some text</ul>some more text'
			],
			'del tag' => [
				'some [s]strike through[/s] text',
				'some <del>strike through</del> text'
			],
			'naked url is converted to link' => [
				'example url: https://example.com',
				'example url: <a href="https://example.com"  target="_blank"  rel="nofollow noopener">https://example.com</a>'
			],
			'naked url within code block is not converted to link' => [
				"[code]\nhttp://example.com\n[/code]",
				"<pre><code>http://example.com</code></pre>"
			],
		];
	}

	/**
	 * Dataprovider for test_bbcode_observer
	 *
	 * @returns an array of arrays with the following fields:
	 *   * `string $src`		- The source string to convert
	 *   * `bool $logged_in`	- Whether we should test with a logged in user
	 *   * `string $lang`		- The language code of the simulated user
	 *   * `string $expected`	- The expecte result of the conversion.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public static function bbcode_observer_provider(): array {
		return [
			'authenticated observer' => [
				'[observer=1]This should be visible[/observer][observer=0]but not this[/observer]',
				true,
				'en',
				'This should be visible',
			],
			'unauthenticated observer' => [
				'[observer=1]This should not be visible[/observer][observer=0]but this should be![/observer]',
				false,
				'en',
				'but this should be!',
			],
			'authenticated observer.language matching' => [
				'[observer.language=nb]Kun på norsk[/observer][observer.language!=nb]To everybody else[/observer]',
				true,
				'nb',
				'Kun på norsk',
			],
			'authenticated observer.language no match' => [
				'[observer.language=nb]Kun på norsk[/observer][observer.language!=nb]To everybody else[/observer]',
				true,
				'en',
				'To everybody else',
			],
			'multiple observer blocks' => [
				'[observer=1]This should be visible,[/observer][observer=0] but not this,[/observer][observer=1] and this as well.[/observer]',
				true,
				'en',
				'This should be visible, and this as well.',
			],
			'authenticated observer rpost' => [
				'[rpost=a title]This is the body[/rpost]',
				true,
				'en',
				'<a href="https://example.com:666/rpost?f=&title=a+title&body=This+is+the+body"  target="_blank"  rel="nofollow noopener">https://example.com:666/rpost?f=&title=a+title&body=This+is+the+body</a>',
			],
			'unauthenticated observer rpost' => [
				'[rpost=a title]This is the body[/rpost]',
				false,
				'en',
				'',
			],
		];
	}

	/**
	 * Dataprovider for test_html2bbcode.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public static function html2bbcode_provider(): array {
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
			'code block with URL' => [
				'<pre><code>\nproxy_pass http://example.com\n</code></pre>',
				'[code]\nproxy_pass http://example.com\n[/code]'
			],
			'paragraph with a mention and some text' => [
				'<p><span class="h-card" translate="no"><a href="https://example.org/@profile" class="u-url mention">@<span>profile</span></a></span> some content</p>',
				'[url=https://example.org/@profile]@profile[/url] some content'
			],
			'nested tags with ampersand and new line' => [
				"<b>\n<i>foo & bar</i></b>",
				'[b] [i]foo &amp; bar[/i][/b]'
			],
			'html reshares from streams' => [
				'<div><div><a href="https://example.com"><img src="https://example.com/image.jpg" alt="image/photo"></a> shared something</div>something</div>',
				'[url=https://example.com][img=https://example.com/image.jpg]image/photo[/img][/url] shared something' . "\n" . 'something'
			],
			'list' => [
				'<ul><li>list 1</li><li>list 2</li><li>list 3</li></ul>',
				'[list][*]list 1[*]list 2[*]list 3[/list]'
			],
			'list with paragraph' => [
				'<ul><li><p>list 1</p></li><li><p>list 2</p></li><li><p>list 3</p></li></ul>',
				'[list][*]list 1[*]list 2[*]list 3[/list]'
			],
			'nested list' => [
				'<ul><li>list 1</li><li>list 2</li><li>list 3</li><ul><li>list 1</li><li>list 2</li><li>list 3</li></ul></ul>',
				'[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[/list][/list]'
			],
			'double nested list' => [
				'<ul><li>list 1</li><li>list 2</li><li>list 3</li><ul><li>list 1</li><li>list 2</li><li>list 3</li><ul><li>list 1</li><li>list 2</li><li>list 3</li></ul></ul></ul>',
				'[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[/list][/list][/list]'
			],
			'list without closing li' => [
				'<ul><li>list 1<li>list 2<li>list 3</ul>',
				'[list][*]list 1[*]list 2[*]list 3[/list]'
			],
			'nested list without closing li' => [
				'<ul><li>list 1<li>list 2<li>list 3<ul><li>list 1<li>list 2<li>list 3</ul></ul>',
				'[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[/list][/list]'
			],
			'double nested list without closing li' => [
				'<ul><li>list 1<li>list 2<li>list 3<ul><li>list 1<li>list 2<li>list 3<ul><li>list 1<li>list 2<li>list 3</ul></ul></ul>',
				'[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[list][*]list 1[*]list 2[*]list 3[/list][/list][/list]'
			],
			'del tag' => [
				'some <del>strike through</del> text',
				'some [s]strike through[/s] text'
			],
			'table' => [
				'<table><tr><td>row1, col1</td><td>row1, col2</td></tr><tr><td>row2, col1</td><td>row2, col2</td></tr></table>',
				'[table][tr][td]row1, col1[/td][td]row1, col2[/td][/tr][tr][td]row2, col1[/td][td]row2, col2[/td][/tr][/table]'
			]
		];
	}
}

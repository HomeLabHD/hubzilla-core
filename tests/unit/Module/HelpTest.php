<?php
/**
 * SPDX-FileCopyrightText: 2024 Harald Eilertsen
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

class HelpTest extends \Zotlabs\Tests\Unit\Module\TestCase {

	use \phpmock\phpunit\PHPMock;

	/**
	 * Define the stubs to make sure they work later in the test.
	 *
	 * @see https://php-mock.github.io/php-mock-phpunit/api/class-phpmock.phpunit.PHPMock.html#_defineFunctionMock
	 *
	 * @beforeClass
	 */
	public static function define_stubs(): void {
		self::defineFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		self::defineFunctionMock('Zotlabs\Module', 'file_get_contents');
	}

	/**
	 * Test getting a help page when the underlying file exists.
	 *
	 * @testWith
	 *	["md"]
	 *	["bb"]
	 *	["html"]
	 */
	public function test_get_request_when_help_file_exists(string $ext): void {
		// Stub file exists, to only retur true for the file with the current
		// extension
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturnCallback(
				fn (string $path) => $path === "doc/en/about/help_topic.{$ext}"
			);

		// Use a value map to make the `file_get_contents` stub return the
		// correct content for the file types.
		$file_content_map = [
			[ 'doc/en/about/help_topic.md', "### Help heading\n\$Projectname help content" ],
			[ 'doc/en/about/help_topic.bb', "[h3]Help heading[/h3]\n\n\$Projectname help content" ],
			[ 'doc/en/about/help_topic.html', "<h3>Help heading</h3><p>\$Projectname help content</p>" ],
		];

		// Stub `file_get_contents` to plant our own content.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Module', 'file_get_contents');
		$fgc_stub
			->expects($this->once())
			->willReturn($this->returnValueMap($file_content_map));


		$this->get("help/about/help_topic");

		// Check that markdown content was correctly rendered
		$this->assertPageContains('<h3>Help heading</h3>');

		// Check that `$Projectname` has been translated properly
		$this->assertPageContains('Hubzilla help content');

		// Check that heading has been set
		$this->assertPageContains('Hubzilla Documentation: About');

		// Check that page title has been set
		$this->assertTrue(isset(\App::$page['title']), 'Page title not set');
		$this->assertStringContainsString('Help', \App::$page['title']);
		$this->assertStringContainsStringIgnoringCase('Help Topic', \App::$page['title']);

		// Check that nav selection has been set
		$this->assertTrue(isset(\App::$nav_sel['raw_name']), 'Nav selection raw name not set');
		$this->assertEquals('Help', \App::$nav_sel['raw_name']);

		$this->assertTrue(isset(\App::$nav_sel['name']), 'Navselection name not set');
		$this->assertEquals('Help', \App::$nav_sel['name']);
	}

	public function test_get_request_should_return_404_when_help_file_does_not_exist(): void {
		// Stub file exists, to only retur true for the file with the current
		// extension
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturn(false);

		// Make sure `file_get_contents` is never called by the code.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Module', 'file_get_contents');
		$fgc_stub->expects($this->never());

		$this->get("help/this_topic_does_not_exist");

		$this->assertPageContains('not found');
	}

	public function test_get_request_without_args_redirects_to_about_page(): void {
		$this->stub_goaway();
		$this->expectException(\Zotlabs\Tests\Unit\Module\RedirectException::class);
		$this->expectExceptionMessage('about/about');

		$this->get('help');
	}

	public function test_find_help_file_returns_first_match(): void {
		// Stub file exists, to always return true
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->once())
			->willReturn(true);

		// Stub `file_get_contents` to plant our own content.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Module', 'file_get_contents');
		$fgc_stub
			->expects($this->once())
			->with('doc/en/first.md')
			->willReturn('found');

		$this->get('help/first');
	}

	public function test_includes(): void {
		// Stub `file_get_contents` to plant our own content.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Module', 'file_get_contents');
		$fgc_stub
			->expects($this->any())
			->willReturnCallback(
				function (string $path): string {
					if ($path === 'doc/en/sub.md') {
						return "### This is the included file.";
					} else {
						return "### Main topic\n\n#include doc/en/sub.md;";
					}
				}
			);

		// Stub file exists, to always return true
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturn(true);

		$this->get('help/main');

		$this->assertPageContains('<h3>This is the included file.</h3>');
	}

	public function test_include_file_of_different_type_than_main_file(): void {
		// Stub `file_get_contents` to plant our own content.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Module', 'file_get_contents');
		$fgc_stub
			->expects($this->any())
			->willReturnCallback(
				function (string $path): string {
					if ($path === 'doc/en/sub.md') {
						return "### This is the included file.";
					} else {
						return "[h3]Main topic[/h3]\n\n#include doc/en/sub.md;";
					}
				}
			);

		// Stub file exists, only return true for main.bb and sub.md
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturnCallback(
				fn (string $path) => (
					$path === 'doc/en/main.bb' || $path === 'doc/en/sub.md'
				)
			);

		$this->get('help/main');

		$this->assertPageContains('<h3>This is the included file.</h3>');
	}

	/**
	 * Helper to simplify asserting contents in the rendered page.
	 *
	 * @param string $needle	The expected string to find.
	 */
	private function assertPageContains(string $needle): void {
		$this->assertStringContainsString($needle, \App::$page['content']);
	}
}

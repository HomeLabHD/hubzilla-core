<?php
/**
 * Tests for the Helpindex widget.
 *
 * SPDX-FileCopyrightText: 2024 Harald Eilertsen
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

/**
 * Test class for testing the Helpindex widget.
 */
class HelpindexTest extends \Zotlabs\Tests\Unit\Module\TestCase {

	use \phpmock\phpunit\PHPMock;

	/**
	 * Define the stubs to make sure they work later in the test.
	 *
	 * @see https://php-mock.github.io/php-mock-phpunit/api/class-phpmock.phpunit.PHPMock.html#_defineFunctionMock
	 *
	 * @beforeClass
	 */
	public static function define_stubs(): void {
		\phpmock\phpunit\PHPMock::defineFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		\phpmock\phpunit\PHPMock::defineFunctionMock('Zotlabs\Widget', 'file_get_contents');
	}

	public function test_loading_toc(): void {
		// Stub `file_get_contents` to plant our own content.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Widget', 'file_get_contents');
		$fgc_stub
			->expects($this->once())
			->with($this->equalTo('doc/en/toc.html'))
			->willReturn('toc');

		// Stub `file_exists` to only return true for the english toc file
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturnCallback(fn (string $path) => $path === 'doc/en/toc.html' );

		$this->render_widget();
		$this->assertOutputContains('toc');
		$this->assertOutputContains('Help Content');
	}

	public function test_that_result_is_empty_when_toc_not_present(): void {
		// Ensure `file_get_contents` is not called during the test.
		$fgc_stub = $this->getFunctionMock('Zotlabs\Widget', 'file_get_contents');
		$fgc_stub->expects($this->never());

		// Stub `file_exists` to always return false to simulate that we
		// can't find the toc file.
		$fe_stub = $this->getFunctionMock('Zotlabs\Lib\Traits', 'file_exists');
		$fe_stub
			->expects($this->any())
			->willReturn(false);

		$this->render_widget();
	}

	/**
	 * Initializes the app and calls the widget code.
	 */
	private function render_widget(): void {
		$_GET['q'] = 'help/en/about/about';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		\App::init();

		$widget = new \Zotlabs\Widget\Helpindex();
		$this->output = $widget->widget([]);
	}

	private function assertOutputContains(string $needle): void {
		$this->assertStringContainsString($needle, $this->output);
	}
}

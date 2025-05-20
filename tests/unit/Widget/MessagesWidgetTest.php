<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Widget;

use App;
use Zotlabs\Widget\Messages;
use Zotlabs\Tests\Unit\Module\TestCase;

class MessagesWidgetTest extends TestCase
{
	use \phpmock\phpunit\PHPMock;

	/**
	 * List of file tags should be empty if there are no file tags.
	 */
	public function testNoFileTags(): void
	{
		$local_channe_stub = $this->getFunctionMock('Zotlabs\Widget', 'local_channel')
							->expects($this->any())
							->willReturn(42);

		$feature_enabled_stub = $this->getFunctionMock('Zotlabs\Widget', 'feature_enabled')
							   ->expects($this->any())
							   ->willReturn(true);

		$this->renderWidget();
		$this->assertOutputMatches('|<datalist\s+id="data_filetags">\s+</datalist>|');
	}

	/**
	 * The widget lists file tags that are defined for the channel.
	 */
	public function testFileTagsAreListed(): void
	{
		$local_channe_stub = $this->getFunctionMock('Zotlabs\Widget', 'local_channel')
							->expects($this->any())
							->willReturn(42);

		$feature_enabled_stub = $this->getFunctionMock('Zotlabs\Widget', 'feature_enabled')
							   ->expects($this->any())
							   ->willReturn(true);

		/*
		 * Add a few tags.
		 */
		store_item_tag(42, 1, TERM_OBJ_POST, TERM_FILE, 'test_file_tag', '');
		store_item_tag(42, 1, TERM_OBJ_POST, TERM_FILE, 'test_file_tag2', '');

		$this->renderWidget();
		$this->assertOutputMatches('|<option\s+value="test_file_tag">|');
		$this->assertOutputMatches('|<option\s+value="test_file_tag2">|');
	}

	/**
	 * Initializes the app and calls the widget code.
	 */
	private function renderWidget(): void {
		$_GET['q'] = 'hq';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		App::init();

		$widget = new Messages();
		$this->output = $widget->widget([]);
	}

	/**
	 * Asserts that the output matches a given regex pattern.
	 *
	 * If the pattern does not match, the test will be marked as failed.
	 *
	 * @param string $pattern	The regex that should be matched.
	 */
	private function assertOutputMatches(string $pattern): void {
		$this->assertMatchesRegularExpression($pattern, $this->output);
	}
}

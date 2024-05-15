<?php
/**
 * Unit/integration tests for the Rbmark module.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

class RbmarkTest extends \Zotlabs\Tests\Unit\Module\TestCase {
	public function test_unauthenticated_get_request_return_login_form(): void {
		$lc_stub = $this->getFunctionMock('Zotlabs\Module', 'local_channel');
		$lc_stub
			->expects($this->once())
			->willReturn(false);

		$this->get('rbmark', ['url' => 'https://bookmarked.url']);

		$this->assertPageContains('value="login" />');

		// also check that the original query is saved in the session
		$this->assertEquals('https://bookmarked.url', $_SESSION['bookmark']['url']);
		$this->assertEquals('rbmark', $_SESSION['bookmark']['q']);
	}

	public function test_authenticated_get_request_returns_save_bookmark_form(): void {
		$lc_stub = $this->getFunctionMock('Zotlabs\Module', 'local_channel');
		$lc_stub
			->expects($this->once())
			->willReturn(42);

		$this->get('rbmark', [
			'url' => 'https://bookmarked.url',
			'title' => 'My bookmark',
		]);

		$this->assertPageContains('<form action="rbmark" method="post"');
		$this->assertPageContains('URL of bookmark');
		$this->assertPageContains('value="https://bookmarked.url"');
		$this->assertPageContains('value="My bookmark"');
	}

	public function test_that_params_are_escaped_in_save_bookmark_form(): void {
		$lc_stub = $this->getFunctionMock('Zotlabs\Module', 'local_channel');
		$lc_stub
			->expects($this->once())
			->willReturn(42);

		$this->get('rbmark', [
			'url' => 'https://bookmarked.url" onload="alert(/boom/)',
			'title' => 'My bookmark"><script alert(/boom/);</script>',
		]);

		$this->assertPageContains('value="https://bookmarked.url&quot; onload=&quot;alert(/boom/)');
		$this->assertPageContains('value="My bookmark&quot;&gt;&lt;script alert(/boom/);&lt;/script&gt;');
	}

	public function test_that_existing_bookmark_folders_are_listed(): void {
		$lc_stub = $this->getFunctionMock('Zotlabs\Module', 'local_channel');
		$lc_stub
			->expects($this->once())
			->willReturn(42);

		$menu_id = menu_create([
			'menu_name' => 'My bookmarks',
			'menu_desc' => 'A collection of my bookmarks',
			'menu_flags' => MENU_BOOKMARK,
			'menu_channel_id' => 42,
		]);

		$this->get('rbmark', [
			'url' => 'https://bookmarked.url',
			'title' => 'My bookmark',
		]);

		$this->assertPageContains(
			"<option value=\"{$menu_id}\" >My bookmarks</option>"
		);
	}
}

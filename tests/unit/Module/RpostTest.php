<?php
/**
 * Tests for Rpost module.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

class RpostTest extends \Zotlabs\Tests\Unit\Module\TestCase {

	/**
	 * Basic test of a get request with no args as an authenticated user.
	 */
	public function test_get_with_no_args(): void {
		$this->get_authenticated();

		$this->assertPageContains('<form id="profile-jot-form"');
		$this->assertPageContains('<input type="hidden" name="profile_uid" value="42"');
	}

	/**
	 * Display login form if session is not authenticated.
	 */
	public function test_display_login_form_if_not_logged_in(): void {
		$lc_mock = $this->getFunctionMock('Zotlabs\Module', 'local_channel')
			->expects($this->any())
			->willReturn(false);

		$this->get('rpost');

		$this->assertPageContains('<form action="https://hubzilla.test/rpost" id="main_login"');
	}

	public function test_populates_form_from_query_params(): void {
		$this->get_authenticated([
			'title' => 'This is my title',
			'body' => 'The body of the post',
			'source' => 'The temple of the Dagon',
		]);

		$this->assertPageContains('value="This is my title"');
		$this->assertPageContains('>The body of the post</textarea>');
		$this->assertPageContains('value="The temple of the Dagon"');
	}

	public function test_convert_body_from_html_to_bbcode(): void {
		$this->get_authenticated([
			'body' => "<h1>Awesome page</h1>\r\n<p>Awesome content!</p>",
			'type' => 'html',
		]);

		$this->assertPageContains(">[h1]Awesome page[/h1]\n\nAwesome content!</textarea>");
	}

	/**
	 * Private helper method to perform an authenticated GET request.
	 *
	 * @param array $query	An associative array of query parameters.
	 */
	private function get_authenticated(array $query = []): void {
		// Mock `local_chanel()` to emulate a valid logged in channel
		$lc_mock = $this->getFunctionMock('Zotlabs\Module', 'local_channel')
			->expects($this->any())
			->willReturn(42);

		// Set basic access controls to keep AccessList happy.
		\App::$channel = [
			'channel_id' => 42,
			'channel_location' => null,
			'channel_address' => '',
			'channel_allow_cid' => '',
			'channel_allow_gid' => '',
			'channel_deny_cid' => '',
			'channel_deny_gid' => '',
		];

		$this->get('rpost', $query);
	}
}

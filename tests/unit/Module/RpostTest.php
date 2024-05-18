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
	public function test_get_with_no_args(): void {
		// Mock `local_chanel()` to emulate a valid logged in channel
		$lc_mock = $this->getFunctionMock('Zotlabs\Module', 'local_channel')
			->expects($this->any())
			->willReturn(42);

		// Set basic access controls to keep AccessList happy.
		\App::$channel = [
			'channel_allow_cid' => null,
			'channel_allow_gid' => null,
			'channel_deny_cid' => null,
			'channel_deny_gid' => null,
		];

		$this->get('rpost');

		$this->assertPageContains('<form id="profile-jot-form"');
		$this->assertPageContains('<input type="hidden" name="profile_uid" value="42"');
	}
}

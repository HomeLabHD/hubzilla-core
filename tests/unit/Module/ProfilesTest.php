<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use App;
use PDOStatement;

/**
 * Tests for the Profiles module
 */
class ProfilesTest extends TestCase
{
	public function testDisplaysProfileEditFormIfMultiProfilesDisabled(): void {
		// Set up the channel we'll be making the request for
		$channel = $this->fixtures['channel'][0];
		App::set_channel($channel);

		// And set up the default profile
		$this->createDefaultProfile($channel);

		// Replace calls to 'local_channel' and make sure they return
		// the id of the current channel.
		$local_channel_mock =
			$this->getFunctionMock('Zotlabs\Module', 'local_channel')
				->expects($this->any())
				->willReturn($channel['channel_id']);

		// Replace calls to 'feature_enabled', and make it unconditionally
		// return true. This is called by the Profiles module to check that
		// multiple profiles are enabled.
		$feature_enabled_mock =
			$this->getFunctionMock('Zotlabs\Module', 'feature_enabled')
				->expects($this->any())
				->willReturn(false);

		// Simulate a GET request to the module
		$this->get('profiles');

		// Check that the rendered page has the expected entry.
		$this->assertPageContains('<form id="profile-edit-form"');
	}

	public function testListDefaultProfileWithNoArgs(): void {
		// Set up the channel we'll be making the request for
		$channel = $this->fixtures['channel'][0];
		App::set_channel($channel);

		// And set up the default profile
		$this->createDefaultProfile($channel);

		// Replace calls to 'local_channel' and make sure they return
		// the id of the current channel.
		$local_channel_mock =
			$this->getFunctionMock('Zotlabs\Module', 'local_channel')
				->expects($this->any())
				->willReturn($channel['channel_id']);

		// Replace calls to 'feature_enabled', and make it unconditionally
		// return true. This is called by the Profiles module to check that
		// multiple profiles are enabled.
		$feature_enabled_mock =
			$this->getFunctionMock('Zotlabs\Module', 'feature_enabled')
				->expects($this->once())
				->willReturn(true);

		// Simulate a GET request to the module
		$this->get('profiles');

		// Check that the rendered page has the expected entry.
		$this->assertPageContains('Default Profile');
	}

	/**
	 * Helper function to create the default profile for a channel
	 *
	 * @param array $channel	An associative array containing the channel.
	 */
	private function createDefaultProfile(array $channel): void {
		$res = profile_store_lowlevel([
			'aid'          => intval($channel['channel_account_id']),
			'uid'          => intval($channel['channel_id']),
			'profile_guid' => random_string(),
			'profile_name' => t('Default Profile'),
			'is_default'   => 1,
			'publish'      => true,
			'fullname'     => $channel['channel_name'],
			'photo'        => z_root() . "/photo/profile/l/{$channel['channel_id']}",
			'thumb'        => z_root() . "/photo/profile/m/{$channel['channel_id']}"
		]);

		$this->assertInstanceOf(PDOStatement::class, $res);
		$this->assertEquals('00000', $res->errorCode());
	}
}

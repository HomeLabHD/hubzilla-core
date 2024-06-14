<?php
/**
 * Unit tests for the `create_identity` function.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

class CreateIdentityTest extends UnitTestCase {

	private bool $queueworker_started = false;

	public function test_empty_args() {
		insert_hook('proc_run', [$this, 'proc_run_hook']);
		$result = create_identity([]);
		$this->assertEquals(
			['success' => false, 'message' => 'No account identifier'],
			$result);

		$this->assertFalse($this->queueworker_started);
	}

	public function test_create_new_channel_with_valid_account_id(): void {
		insert_hook('proc_run', [$this, 'proc_run_hook']);
		$result = create_identity([
			'account_id' => $this->fixtures['account'][0]['account_id'],
			'nickname' => 'testuser',
			'name' => 'Olga Testuser',
		]);

		$this->assertTrue($result['success']);
		$this->assertTrue($this->queueworker_started);
	}

	public function test_create_new_channel_with_nnexistant_account_id(): void {
		insert_hook('proc_run', [$this, 'proc_run_hook']);
		$result = create_identity([
			'account_id' => 666,
			'nickname' => 'testuser',
			'name' => 'Olga Testuser',
		]);

		/*
		 * We would expect this fo fail, but...
		 *
		 * The create_identity function will happily create a new channel with an
		 * non-existent account_id. The New_channel module will perform a check
		 * to ensure that only valid (and logged in) accounts can create a new channel.
		 *
		 * This is a bit weak, but for now we let it pass...
		 */
		$this->assertTrue($result['success']);
		$this->assertTrue($this->queueworker_started);
	}

	public function proc_run_hook(array &$args): void {
		$args['run_cmd'] = false;
		$this->queueworker_started =
			$args['args'] === ['php', 'Zotlabs/Daemon/Master.php', 'Queueworker'];
	}
}

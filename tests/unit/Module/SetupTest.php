<?php
/**
 * SPDX-FileCopyrightText: 2024 Harald Eilertsen
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

/**
 * SetupModuleTest
 *
 * The Setup module should only be available during site installation. This is
 * determined by whether there are any accounts present in the database, or
 * not.
 *
 * This is a complex module, so expect the tests to grow as more of it will be
 * covered.
 */
class SetupModuleTest extends \Zotlabs\Tests\Unit\Module\TestCase {

	public function test_that_setup_is_available_if_no_accounts_in_db(): void {
		$this->with_no_accounts_in_db();
		$this->get('setup');

		$this->assertEquals('setup', \App::$page['page_title']);

		// Assert that result _don't_ match "Permission denied"
		$this->assertThat(
			\App::$page['content'],
			$this->logicalNot(
				$this->matchesRegularExpression('/Permission denied/')
			)
		);
	}

	public function test_that_setup_is_not_available_if_accounts_in_db(): void {
		// The fixtures loaded by default add a couple of accounts.
		$this->get('setup');

		$this->assertEquals('setup', \App::$page['page_title']);
		$this->assertMatchesRegularExpression('/Permission denied/', \App::$page['content']);
	}

	public function test_that_setup_testrewrite_returns_ok(): void {
		// We need to stub the `killme` function, as it is called directly from
		// the code under test.
		$this->stub_killme();

		$output = '';

		// Turn on output buffering, as code under test echoes
		// directly to the output
		ob_start();
		try {
			$this->get('setup/testrewrite');
		} catch (\Zotlabs\Tests\Unit\Module\KillmeException) {
			$output = ob_get_contents();
		}

		$this->assertEquals('ok', $output);

		ob_end_clean();
	}

	/**
	 * Delete all accounts from the database.
	 *
	 * This is currently needed because we automatically import the database
	 * fixtures on test start, which contains a couple of accounts already.
	 */
	private function with_no_accounts_in_db(): void {
		q('DELETE FROM account;');
	}
}

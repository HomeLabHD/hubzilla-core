<?php
/**
 * Tests for the authentication code used in Hubzilla.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Includes;

use App;
use Zotlabs\Lib\AConfig;
use Zotlabs\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\{DataProvider, RunTestsInSeparateProcesses};

/**
 * Test class containing the test for the Hubzilla authentication code.
 *
 * Since the main authentication code is executed in the global scope on
 * inclusion of the `includes/auth.php` file, we need to run each test in a
 * separate process to make sure we can excersize the code as we need.
 */
#[RunTestsInSeparateProcesses]
class AuthTest extends UnitTestCase {

	/**
	 * Check that mfa status is not checked for certain modules.
	 *
	 * This causes issues with things like WebDAV and CardDAV, as there's
	 * currently no way for these modules to signal that a TOTP code is needed
	 * back to the connecting client.
	 */
	#[DataProvider('modules_excluded_from_mfa')]
	public function test_mfa_is_not_checked_for_excluded_modules(string $module, array $args): void {
		$account_id = $this->fixtures['account']['0']['account_id'];

		$_SESSION = [
			'authenticated' => true,
			'account_id' => $account_id,

			// Trick the code to not warn that $_SESSION['uid'] is not set,
			// but also not trigger the code that tries to change to the
			// given channel. *Remove when code is fixed!*
			'uid' => 0,
		];

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		App::$session = $this->create_session_stub();
		App::$module = $module;
		App::$argv = $args;
		App::$argc = count($args);

		// Enable multi factor authentication for this account
		AConfig::Set($account_id, 'system', 'mfa_enabled', true);

		require 'include/auth.php';

		$this->assertEquals(1, $_SESSION['authenticated']);
	}

	/**
	 * Data provider for testing modules excluded from mfa
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	public static function modules_excluded_from_mfa(): array {
		return [
			['totp_check', []],
			['cdav', []],
			['cdav', ['calendar']],
			['cdav', ['addressbook']],
			['dav', []],
		];
	}

	private function create_session_stub(): \Zotlabs\Web\Session {
		return $this->createStub('Zotlabs\Web\Session');
	}
}

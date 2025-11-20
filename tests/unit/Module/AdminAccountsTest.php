<?php
/*
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use App;
use PHPUnit\Framework\Attributes\Before;

class AdminAccountsTest extends TestCase {

	protected $stub_check_security;
	protected $stub_is_site_admin;
	protected $stub_goaway;
	protected $stub_notice;

	protected array $notice;

	/**
	 * Set up the stubs common for the tests.
	 */
	#[Before]
	public function setup_stubs(): void {
		$this->stub_check_form_security();
		$this->stub_is_site_admin();
		$this->stub_goaway();
		$this->stub_notice();
	}

	public function test_blocking_accounts_marks_selected_accounts_as_blocked(): void {
		$params = [
			'user' => [ 42 ],
			'blocked' => [ false ],
			'page_accounts_block' => true,
		];

		try {
			$this->post('admin/accounts', [], $params);
		} catch (RedirectException $redirect) {
			$this->assertEquals(z_root() . '/admin/accounts', $redirect->getMessage());
		}

		$account = get_account_by_id(42);
		$this->assertEquals(ACCOUNT_BLOCKED, $account['account_flags'] & ACCOUNT_BLOCKED);

		$this->assertEquals('1 account blocked/unblocked', $this->notice[0]);
	}

	public function test_unblocking_accounts_clears_the_blocked_flag(): void {
		// Pass two users to the module, one that is not blocked,
		// and one that is.
		$params = [
			'user' => [ 42, 44 ],
			'blocked' => [ false, true ],
			'page_accounts_block' => true,
		];

		try {
			$this->post('admin/accounts', [], $params);
		} catch (RedirectException $redirect) {
			$this->assertEquals(z_root() . '/admin/accounts', $redirect->getMessage());
		}

		// We expect the previously unblocked account to be blocked.
		$account = get_account_by_id(42);
		$this->assertEquals(ACCOUNT_BLOCKED, $account['account_flags'] & ACCOUNT_BLOCKED);

		// We expect the previously blocked account to be unblocked.
		$blocked_account = get_account_by_id(44);
		$this->assertEquals(0, $blocked_account['account_flags'] & ACCOUNT_BLOCKED);

		$this->assertEquals('2 account blocked/unblocked', $this->notice[0]);
	}

	public function test_deleting_accounts_remove_them_from_db(): void {
		$params = [
			'user' => [ 42, 44 ],
			'page_accounts_delete' => true,
		];

		App::$account = $this->fixtures['account'][0];

		try {
			$this->post('admin/accounts', [], $params);
		} catch (RedirectException $redirect) {
			$this->assertEquals(z_root() . '/admin/accounts', $redirect->getMessage());
		}

		$this->assertEquals(null, get_account_by_id(42));
		$this->assertEquals(null, get_account_by_id(44));
	}

	public function test_approving_pending_accounts_clears_pending_flag(): void {

		// Catch calls to the php mail function
		//
		// This is just to get it out of the way, we don't care about
		// how many times it's called, or with what args here.
		$this->getFunctionMock('Zotlabs\Lib', 'mail')
			->expects($this->any())
			->willReturn(true);

		$params = [
			'pending' => [
				$this->fixtures['register'][0]['reg_hash'],
				$this->fixtures['register'][1]['reg_hash']
		   	],
			'page_accounts_approve' => true,
		];

		try {
			$this->post('admin/accounts', [], $params);
		} catch (RedirectException $redirect) {
			$this->assertEquals(z_root() . '/admin/accounts', $redirect->getMessage());
		}

		foreach ([45, 46] as $id) {
			$account = get_account_by_id($id);
			$this->assertEquals(0, $account['account_flags'] & ACCOUNT_PENDING);
		}
	}

	/**
	 * Stub the check_form_security_token_ForbiddenOnErr.
	 */
	protected function stub_check_form_security(): void {
		$this->stub_check_security =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'check_form_security_token_redirectOnErr')
				->expects($this->once())
				->with(
					$this->identicalTo('/admin/accounts'),
					$this->identicalTo('admin_accounts'))
				->willReturn(true);
	}

	/**
	 * Stub the call to is_site_admin in the Admin main module.
	 */
	protected function stub_is_site_admin(): void {
		$this->stub_is_site_admin =
			$this->getFunctionMock('Zotlabs\Module', 'is_site_admin')
				->expects($this->once())
				->willReturn(true);
	}

	/**
	 * Stub the goaway function.
	 *
	 * Will throw an RedirectException with the URL being redirected to
	 * as the exception message.
	 *
	 * @throws RedirectException
	 */
	protected function stub_goaway(): void {
		$this->stub_goaway =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'goaway')
				->expects($this->once())
				->willReturnCallback(function (string $uri) {
					throw new RedirectException($uri);
				});
	}

	protected function stub_notice(): void {
		$this->notice = [];
		$this->stub_notice =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'notice')
				->expects($this->any())
				->willReturnCallback(function (string $arg) {
					$this->notice[] = $arg;
				});
	}
}

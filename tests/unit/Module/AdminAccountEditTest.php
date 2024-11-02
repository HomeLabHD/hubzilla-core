<?php
/* Tests for Account_edit module
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\{Before, After};
use Zotlabs\Model\Account;

class AdminAccountEditTest extends TestCase {

	#[Before]
	public function setup_mocks(): void {
		/*
		 * As we're testing pages that should only be reachable by the
		 * site admin, it makes no sense to have it return anything else
		 * than true.
		 */
		$this->stub_is_site_admin =
			$this->getFunctionMock('Zotlabs\Module', 'is_site_admin')
				->expects($this->once())
				->willReturn(true);

		$this->info = [];
		$this->stub_info =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'info')
				->expects($this->any())
				->willReturnCallback(function (string $arg) {
					$this->info[] = $arg;
				});

		$this->notice = [];
		$this->stub_notice =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'notice')
				->expects($this->any())
				->willReturnCallback(function (string $arg) {
					$this->notice[] = $arg;
				});

	}

	#[After]
	public function tear_down_mocks(): void {
		$this->stub_is_site_admin = null;
		$this->stub_info = null;
		$this->stub_notice = null;
		$this->stub_check_security = null;
		$this->stub_get_form_security_token = null;
	}

	public function test_rendering_admin_account_edit_page(): void {
		$this->stub_get_form_security_token =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'get_form_security_token')
				->expects($this->once())
				->willReturn('the-csrf-token');

		$account = $this->fixtures['account'][0];

		$this->get("admin/account_edit/{$account['account_id']}");

		$this->assertPageContains("<form action=\"admin/account_edit/{$account['account_id']}\" method=\"post\"");
		$this->assertPageContains($account['account_email']);

		// Check that we generate a CSRF token for the form
		$this->assertPageContains("<input type=\"hidden\" name=\"security\" value=\"the-csrf-token\"");
	}

	public function test_rendering_admin_account_edit_page_fails_if_id_is_not_found(): void {
		$this->get("admin/account_edit/666");

		$this->assertEquals('', \App::$page['content']);
	}

	public function test_rendering_admin_account_edit_page_fails_if_id_is_not_numeric(): void {
		$this->get("admin/account_edit/66invalid");

		$this->assertEquals('', \App::$page['content']);
	}

	public function test_post_empty_form_does_not_modify_account(): void {
		$this->stub_goaway();
		$this->stub_check_form_security(true);

		$account = get_account_by_id($this->fixtures['account'][0]['account_id']);

		try {
			$this->post(
				"admin/account_edit/{$account['account_id']}",
				[],
				[
					'aid' => $account['account_id'],
					'pass1' => '',
					'pass2' => '',
					'service_class' => $account['account_service_class'],
					'account_language' => $account['account_language'],
					'security' => 'The security token',
				]
			);
		} catch (RedirectException $ex) {
			$this->assertEquals(z_root() . '/admin/accounts', $ex->getMessage());
		}

		$reloaded = get_account_by_id($account['account_id']);

		$this->assertEquals($account, $reloaded);

		// Not sure if this is expected behaviour, but this is how it is today.
		$this->assertContains('Account settings updated.' . EOL, $this->info);
	}

	public function test_post_form_changes_account(): void {
		$this->stub_goaway();
		$this->stub_check_form_security(true);

		// clone account from fixture, to ensure it's not replaced with
		// the reloaded one below.
		$account = get_account_by_id($this->fixtures['account'][0]['account_id']);

		try {
			$this->post(
				"admin/account_edit/{$account['account_id']}",
				[],
				[
					'aid' => $account['account_id'],
					'pass1' => 'hunter2',
					'pass2' => 'hunter2',
					'service_class' => 'Some other class',
					'account_language' => 'nn',
					'security' => 'The security token',
				]
			);
		} catch (RedirectException $ex) {
			$this->assertEquals(z_root() . '/admin/accounts', $ex->getMessage());
		}

		$reloaded = get_account_by_id($account['account_id']);

		$this->assertNotEquals($account, $reloaded);
		$this->assertEquals('Some other class', $reloaded['account_service_class']);
		$this->assertEquals('nn', $reloaded['account_language']);

		$now = new DateTimeImmutable('now');
		$this->assertEquals($now->format('Y-m-d H:i:s'), $reloaded['account_password_changed']);

		$this->assertContains('Account settings updated.' . EOL, $this->info);
		$this->assertContains("Password changed for account {$account['account_id']}." . EOL, $this->info);
	}

	public function test_form_with_missing_or_incalid_csrf_token_is_rejected(): void {
		$this->expectException(KillmeException::class);

		// Emulate a failed CSRF check
		$this->stub_check_form_security(false);

		$account_id = $this->fixtures['account'][0]['account_id'];

		$this->post(
			"admin/account_edit/{$account_id}",
			[],
			[
				'aid' => $account_id,
				'pass1' => 'hunter2',
				'pass2' => 'hunter2',
				'service_class' => 'Some other class',
				'account_language' => 'nn',
				'security' => 'Invalid security token',
			]
		);
	}

	/*
	 * Override the stub_goaway method because we need the stub to live in the
	 * Admin namespace.
	 */
	protected function stub_goaway(): void {
		$this->goaway_stub = $this->getFunctionMock('Zotlabs\Module\Admin', 'goaway')
			->expects($this->once())
			->willReturnCallback(
				function (string $uri) {
					throw new RedirectException($uri);
				}
			);
	}

	/**
	 * Stub the check_form_security_token_ForbiddenOnErr.
	 *
	 * In these tests we're not really interested in _how_ the form security
	 * tokens work, but that the code under test perform the checks. This stub
	 * allows us to do that without having to worry if everything is set up so
	 * that the real function would work or not.
	 *
	 * @param bool $valid		true if emulating a valid token, false otherwise.
	 */
	protected function stub_check_form_security(bool $valid): void {
		$this->stub_check_security =
			$this->getFunctionMock('Zotlabs\Module\Admin', 'check_form_security_token_ForbiddenOnErr')
				->expects($this->once())
				->with(
					$this->identicalTo('admin_account_edit'),
					$this->identicalTo('security'))
				->willReturnCallback(function () use ($valid) {
					if (! $valid) {
						throw new KillmeException();
					}
				});
	}
}

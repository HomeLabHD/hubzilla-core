<?php
/**
 * Tests for the Magic module
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use PHPUnit\Framework\Attributes\BackupStaticProperties;
use Zotlabs\Module\Magic;
use App;

class MagicTest extends TestCase {

	public function test_init_with_no_args(): void {

		// We expect the request to end with a status 400, as we do not
		// pass any of the required params.
		//
		// To catch that, we have to mock the call to `http_status_exit`
		// made by the code under test.
		$this->getFunctionMock('Zotlabs\Module', 'http_status_exit')
			->expects($this->once())			// Expect the function to be called only once!
			->with(								// ... with the following two arguments
				$this->identicalTo(400),
				$this->identicalTo('Bad Request')
			)
			->willReturnCallback(function () {	// Run this function instead when called
				throw new KillmeException;		// Throw an exception to terminate processing
			});

		// Tell the test system we excpect this exception to be thrown
		$this->expectException(KillmeException::class);

		$this->get('magic');
	}

	#[BackupStaticProperties(App::class)]
	public function test_local_request_without_delegate(): void {
		$baseurl = 'https://hubzilla.test';
		$dest_url = $baseurl . '/channel/testuser';

		App::set_baseurl($baseurl);

		App::set_observer([
			'xchan_hash' => 'the hash',
		]);

		// We pass a local URL, and have a valid observer, but as the
		// delegate param is not passed, nothing will be done except
		// redirecting to the passed dest url.
		//
		// This should probably return a 400 Invalid Request instead.
		$this->expectRedirectTo($dest_url);

		$this->get('magic', [ 'bdest' => bin2hex($dest_url) ]);
	}

	#[BackupStaticProperties(App::class)]
	public function test_delegate_request_switches_channel_when_allowed(): void {
		$baseurl = 'https://hubzilla.test';
		$dest_url = $baseurl . '/channel/testuser';

		// Set the stage:
		// Populate the global static App class with necessary values for the
		// code under test
		App::set_baseurl($baseurl);
		App::$timezone = 'UTC';

		// Simulate a foreign (to this hub) observer,
		App::set_observer([
			'xchan_hash' => 'foreign hash',
		]);

		// Create the channel the foreign observer wants to access
		$result = create_identity([
			'account_id' => $this->fixtures['account'][0]['account_id'],
			'nickname' => 'testuser',
			'name' => 'Trish Testuser',
		]);

		// Shortcut the permission checks, by saying this observer is allowed
		// the delegate privilege over the target channel
		insert_hook('perm_is_allowed', function (array &$perm) {
			$perm['result'] = true;
		});

		// Add some dummy session data, so we can check that it's being
		// pushed to the delegate session.
		$original_session = [
			'data' => 'Just some test session data',
		];

		$_SESSION = $original_session;

		// Handle redirects manually, since we want to be able to check some
		// assertions after the redirect is thrown.
		$this->stub_goaway();

		try {
			// Send a request to get delegate privileges for the `testuser` channel
			// on the local hub.
			$this->get('magic', [
				'bdest' => bin2hex($dest_url),
				'delegate' => 'testuser@hubzilla.test']
			);
		} catch (RedirectException $e) {
			$this->assertEquals($dest_url, $e->getMessage());
			$this->assertEquals($result['channel']['channel_id'], App::$channel['channel_id']);
			$this->assertEquals($original_session, $_SESSION['delegate_push']);
			$this->assertEquals($result['channel']['channel_id'], $_SESSION['delegate_channel']);
			$this->assertEquals('foreign hash', $_SESSION['delegate']);
			$this->assertEquals($this->fixtures['account'][0]['account_id'], $_SESSION['account_id']);
		}
	}
}

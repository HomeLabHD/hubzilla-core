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

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\Attributes\Before;

class CreateIdentityTest extends UnitTestCase {

	use PHPMock;

	//
	// Static private key used by the openssl stubs.
	//
	private const PRVKEY = <<<'PRVKEY'
			-----BEGIN PRIVATE KEY-----
			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
			0UH3yDnLoPuCvHeX2WAXdyWob6vBOA==
			-----END PRIVATE KEY-----
			PRVKEY;

	//
	// Static public key corresponding to self::PRVKEY, used by the
	// openssl_stubs
	//
	private const PUBKEY = <<<'PUBKEY'
			-----BEGIN PUBLIC KEY-----
			MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAvIcMderJEy61sNlpW00h
			1lF3KRVKV48heeClWtJ/rJql4yHSaAtC1ce1SS9fLX66evbAXy3ClKm8iPQ9We/P
			F6jzijyWj4w2CMB5GSX/mDq7z0D/eaAI0kcBQTcTSMypMr9vttgqJV9WWDT3uqw+
			VwxXV7Aic8UzMduv/rl4j3tx5EP319WUiTNqnBgXZpffCm1zONo3fi3q5kpZuRL0
			d28aYpXzyKt1kOBQe31OUVHt57imhVYZfpLVI7kp4tAxfbbRp5KDj5v0F0ElvwGy
			N+5KX5G3KhCRiMdJ1mBLFWeXE7PeJQSvxIhCD5CTTgoSjjzuzsqd/WNUJdze9q+p
			9xQzzGasQio1/kCPGmJLJhqpsvHF+YiMPgIP86w3wWwqAbO1imS/QgdqTMWT1/7T
			mLx22JMK/vIyZ5ZlOjOF/tBBi0Ls2iBwfl1uLRm1ILfXc2kwX7byWxmE5cFENLe+
			C3gzFLL7gsU9rLx5lTMg+mNWFT6OLZZUeNe9AN4muziXWeMpI/wAabEaAc6F67Cp
			Vv/VKhFbEB9S7rddzDC3vsSfVypa/Sqq+EBmMeTqIT5+PBMpVVA+/O2wzef44+pE
			NFFB3eoCswxl+S22A75OtJ1MuCh7D5rCMR7ptLWCt4YQ9kgxfOPjZzbvJEkm25+Z
			ZAjxDyWHEoxQr8oS6Fg1Lz8CAwEAAQ==
			-----END PUBLIC KEY-----
			PUBKEY;

	private bool $queueworker_started = false;

	#[Before]
	public function setup_openssl_mocks(): void {
		//
		// The `create_identity` function will call `Crypto::new_keypair()`,
		// which again will call `openssl_pkey_new`, to create a new
		// private/public keypair for the channel. While this is safe to call
		// from the code under test, it's also slow.
		//
		// Since we don't really care about the cryptography here, we can make
		// the tests run a bit faster by stubbing the slow function call and
		// return a sentinel value instead of actually generaing the keys.
		//
		$openssl_pkey_new =
			$this->getFunctionMock('Zotlabs\Lib', 'openssl_pkey_new')
			   ->expects($this->any())
			   ->willReturn('openssl_pkey_result');

		//
		// Since we have stubbed `openssl_pkey_new` we also need to mock the
		// call to `openssl_pkey_export` that is used to get the private key
		// from the result of `openssl_pkey_new`.
		//
		// As this function returns the value in a output param, we need to
		// replace it with a callback function to simulate it.
		//
		$openssl_pkey_export =
			$this->getFunctionMock('Zotlabs\Lib', 'openssl_pkey_export')
				->expects($this->any())
				->willReturnCallback(function (string $result, string &$response): bool {
					$this->assertEquals('openssl_pkey_result', $result);
					$response = self::PRVKEY;
					return true;
				});

		//
		// Finally we also need to stub the `openssl_pkey_get_details` function
		// used to retrevive the public key from the `openssl_pkey_new` result.
		//
		$openssl_pkey_get_details =
			$this->getFunctionMock('Zotlabs\Lib', 'openssl_pkey_get_details')
				->expects($this->any())
				->with($this->identicalTo('openssl_pkey_result'))
				->willReturn([ 'key' => self::PUBKEY ]);
	}

	public function test_empty_args_fails_with_an_error_message() {
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

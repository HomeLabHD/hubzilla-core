<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Lib;

use Zotlabs\Lib\Crypto;
use Zotlabs\Lib\LDSignatures;
use Zotlabs\Tests\Unit\UnitTestCase;

class LDSignaturesTest extends UnitTestCase {

	public function testVerifyLDSignature(): void {
		$channel = $this->fixtures['channel'][1];

		$activity = [
			'@context' => 'https://www.w3.org/ns/activitystreams',
			'type' => 'Like',
			'actor' => 'https://hubzilla.test/channel/test',
			'to' => [
				'https://hatchat.example/sarah/',
				'https://example.com/peeps/john/',
			],
			'object' => [
				'@context' => ['@language' => 'en'],
				'id' => 'https://example.org/~alice/note/23',
				'type' => 'Note',
				'attributedTo' => 'https://example.org/~alice',
				'content' => "I'm a goat",
			],
			'signature' => [
				'@context' => [
					'https://www.w3.org/ns/activitystreams',
					'https://w3id.org/security/v1'
				],
				'type' => 'RsaSignature2017',
				'nonce' => 'e3158dce627da99138d238805d2f129db05bd65efce0f712de494565c852f7fc',
				'created' => '2026-05-01T09:22:05Z',
				'creator' => 'https://hubzilla.test/channel/test',
				'signatureValue' =>
					'iFo/j+sG4h7zDiVis8FMOaJvw7ZanbsPdUrS5WTUkcMzL/dg3xPPpt9pKREhVx73ne6y6tIjzq6TWzWEpYvUFg=='
			],
		];

		$this->assertTrue(LDSignatures::verify($activity, $channel['channel_pubkey']));


		// Verify that the signature is independent of the order
		// of the fields
		$activity_2 = [
			'type' => 'Like',
			'@context' => 'https://www.w3.org/ns/activitystreams',
			'actor' => 'https://hubzilla.test/channel/test',
			'to' => [
				'https://example.com/peeps/john/',
				'https://hatchat.example/sarah/',
			],
			'signature' => [
				'nonce' => 'e3158dce627da99138d238805d2f129db05bd65efce0f712de494565c852f7fc',
				'@context' => [
					'https://www.w3.org/ns/activitystreams',
					'https://w3id.org/security/v1'
				],
				'created' => '2026-05-01T09:22:05Z',
				'type' => 'RsaSignature2017',
				'creator' => 'https://hubzilla.test/channel/test',
				'signatureValue' =>
					'iFo/j+sG4h7zDiVis8FMOaJvw7ZanbsPdUrS5WTUkcMzL/dg3xPPpt9pKREhVx73ne6y6tIjzq6TWzWEpYvUFg=='
			],
			'object' => [
				'@context' => ['@language' => 'en'],
				'type' => 'Note',
				'attributedTo' => 'https://example.org/~alice',
				'id' => 'https://example.org/~alice/note/23',
				'content' => "I'm a goat",
			],
		];

		$this->assertFalse($activity == $activity_2);
		$this->assertTrue(LDSignatures::verify($activity_2, $channel['channel_pubkey']));
	}
}

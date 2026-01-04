<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

use Zotlabs\Lib\Libzot;

class XChanTest extends UnitTestCase {

	public function testXChanFetchShouldRejectInvalidArgs(): void {
		$addr = 'example';
		$guid = Libzot::new_uid($addr);
		$hash = Libzot::make_xchan_hash($guid, 'dummy-public-key');

		xchan_store_lowlevel([
			'xchan_hash' => $hash,
			'xchan_guid' => $guid,
			'xchan_addr' => $addr,
		]);

		// Trivial SQL Injection
		$this->assertFalse(xchan_fetch(['hash' => "{$hash}' or 1=1; -- "]));
		$this->assertFalse(xchan_fetch(['guid' => "{$guid}' or 1=1; -- "]));
		$this->assertFalse(xchan_fetch(['address' => "{$addr}' or 1=1; -- "]));

		// Not a valid key
		$this->assertFalse(xchan_fetch(['wrongkey' => $hash]));
	}
}

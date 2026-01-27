<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

use PHPUnit\Framework\Attributes\Before;
use Zotlabs\Lib\Libzot;

class XChanTest extends UnitTestCase {

	private string $addr;
	private string $guid;
	private string $hash;

	#[Before]
	public function populateXChans(): void {
		$this->addr = 'example';
		$this->guid = Libzot::new_uid($this->addr);
		$this->hash = Libzot::make_xchan_hash($this->guid, 'dummy-public-key');

		xchan_store_lowlevel([
			'xchan_hash' => $this->hash,
			'xchan_guid' => $this->guid,
			'xchan_addr' => $this->addr,
		]);
	}

	public function testXChanFetchReturnsStoredXchan(): void {
		$byHash = xchan_fetch(['hash' => $this->hash]);
		$this->assertIsArray($byHash);
		$this->assertEquals($this->addr, $byHash['address']);
		$this->assertEquals($this->guid, $byHash['guid']);
		$this->assertEquals($this->hash, $byHash['hash']);

		$byGuid = xchan_fetch(['guid' => $this->guid]);
		$this->assertIsArray($byGuid);
		$this->assertEquals($this->addr, $byGuid['address']);
		$this->assertEquals($this->guid, $byGuid['guid']);
		$this->assertEquals($this->hash, $byGuid['hash']);

		$byAddr = xchan_fetch(['address' => $this->addr]);
		$this->assertIsArray($byAddr);
		$this->assertEquals($this->addr, $byAddr['address']);
		$this->assertEquals($this->guid, $byAddr['guid']);
		$this->assertEquals($this->hash, $byAddr['hash']);
	}

	public function testXChanFetchShouldRejectInvalidArgs(): void {
		// Trivial SQL Injection
		$this->assertFalse(xchan_fetch(['hash' => "{$this->hash}' or 1=1; -- "]));
		$this->assertFalse(xchan_fetch(['guid' => "{$this->guid}' or 1=1; -- "]));
		$this->assertFalse(xchan_fetch(['address' => "{$this->addr}' or 1=1; -- "]));

		// Not a valid key
		$this->assertFalse(xchan_fetch(['wrongkey' => $this->hash]));

		// Empty values
		$this->assertFalse(xchan_fetch(['hash' => '']));
		$this->assertFalse(xchan_fetch(['guid' => '']));
		$this->assertFalse(xchan_fetch(['address' => '']));

		// Non existing xchan hash/guid/address
		$this->assertFalse(xchan_fetch([
			'hash' => Libzot::make_xchan_hash(Libzot::new_uid('nobody'), 'another-dummy-key')
		]));
		$this->assertFalse(xchan_fetch(['guid' => Libzot::new_uid('nobody')]));
		$this->assertFalse(xchan_fetch(['address' => 'nobody']));
	}
}

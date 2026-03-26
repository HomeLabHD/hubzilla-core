<?php
/* Test cases for DbStats
 *
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Lib;

use DBA;
use Zotlabs\Lib\DbStats;
use Zotlabs\Tests\Unit\UnitTestCase;

class DbStatsTest extends UnitTestCase
{
	public function testGetQueries(): void {
		$stats = DbStats::getStats();
		$this->assertNotNull($stats);
		$this->assertInstanceOf(DbStats::class, $stats);

		$numQueries = $stats->getQueries();
		$this->assertNotEquals(0, $numQueries);

		if (!DBA::$dba->is_postgres()) {
			//
			// Postgres will only update the stats once the transaction
			// is committed or rolled back. As we wrap the tests in a
			// transaction to begin with, the stats won't change here,
			// so we skip this test on Postgres.
			//
			dbq("select * from account");
			dbq("select * from channel");
			dbq("select * from xchan");

			$numMoreQueries = $stats->getQueries();
			$this->assertGreaterThan($numQueries, $numMoreQueries);
		}
	}
}

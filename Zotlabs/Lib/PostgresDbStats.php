<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use DBA;

/**
 * Concrete implementation for getting stats from PostgreSQL databases.
 */
class PostgresDbStats extends DbStats {

	public function getQueries(): int {
		$sqlGetQps = <<<'SQL'
				select (xact_commit + xact_rollback) as queries
				from pg_stat_database
				where datname='%s'
				SQL;

		$result = q($sqlGetQps, DBA::$dba->dbname);
		if (!empty($result)) {
			return $result[0]['queries'] ?? -1;
		}

		return 0;
	}
}

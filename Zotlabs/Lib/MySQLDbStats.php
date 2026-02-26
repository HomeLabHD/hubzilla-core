<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use DBA;
use PDO;

/**
 * Concrete implementation for getting stats from MySQL and MariaDB databases.
 */
class MySQLDbStats extends DbStats {

	public function getQueries(): int {
		//
		// We can't use the regular Hubzilla db helper function here, as
		// it will only return information from a `SELECT` statement.
		//
		// Use the basic PDO access instead.
		//
		$query = DBA::$dba->db->prepare('SHOW STATUS LIKE "Queries"');
		$query->execute();

		$result = $query->fetch(PDO::FETCH_ASSOC);
		logger(print_r($result, true));
		if (!empty($result)) {
			return $result['Value'] ?? -1;
		}

		return 0;
	}
}

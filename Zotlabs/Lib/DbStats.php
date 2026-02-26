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
 * Abstract class to obtain statistics from the database.
 *
 * This class should not be instantiated on it's own, but you can get
 * a concrete class for the configured database type of this site by
 * calling the `DbStats::getStats()` function.
 */
abstract class DbStats {

	/**
	 * Get an object for getting statistics from the database.
	 *
	 * @return DbStats	The concrete class for obtaining the statistics from
	 *                  this instances database.
	 */
	public static function getStats(): DbStats {
		return DBA::$dba->is_postgres()
			? new PostgresDbStats()
			: new MySQLDbStats();
	}

	/**
	 * Return the number of queries recorded by the database.
	 *
	 * @return int Number of queries.
	 */
	public abstract function getQueries(): int;

	// Prevent instantiation of this class
	private function __construct() {}
}

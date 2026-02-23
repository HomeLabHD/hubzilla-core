<?php
/* Handler for perfstats requests.
 *
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Module;

use DBA;
use Zotlabs\Lib\Queue;
use Zotlabs\Lib\QueueWorkerStats;
use Zotlabs\Web\Controller;

class Perfstats extends Controller
{
	public function init(): void {
		$data = $this->getStats();
		json_return_and_die($data);
	}

	private function getStats(): array {
		$stats = [];

		if (function_exists('sys_getloadavg')) {
			$stats['loadavg'] = implode(' / ', sys_getloadavg());
		}

		$stats['dbqueries'] = $this->getNumQueries();
		$stats['outqueue'] = Queue::get_undelivered();

		$qwstats = new QueueWorkerStats();
		$stats['queueworkers'] = $qwstats->active;
		$stats['workqsz'] = $qwstats->size;

		return $stats;
	}

	private function getNumQueries(): int {
		static $sqlGetQps = <<<'SQL'
			select sum(xact_commit + xact_rollback) as sum
			from pg_stat_database
			where datname='%s'
			SQL;

		$result = q($sqlGetQps, DBA::$dba->dbname);
		if (!empty($result)) {
			return $result[0]['sum'] ?? -1;
		}

		return 0;
	}
}

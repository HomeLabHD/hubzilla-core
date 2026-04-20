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
use Zotlabs\Lib\DbStats;
use Zotlabs\Lib\Queue;
use Zotlabs\Lib\QueueWorkerStats;
use Zotlabs\Lib\SystemProfiler;
use Zotlabs\Web\Controller;

/**
 * Controller for the `/perfstats` module.
 *
 * Collects various performance stats for the site, and reponds with the stats
 * as a json array.
 */
class Perfstats extends Controller
{
	public function init(): void {
		//
		// We only accept GET requests
		//
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
			http_status_exit(400, 'Unsupported method');
		}

		//
		// We only accept json requests
		//
		if (getBestSupportedMimeType(['application/json']) === null) {
			http_status_exit(400, 'No supported format');
		}

		//
		// Only admins should be given access
		//
		if (!is_site_admin()) {
			http_status(401, 'Access denied');
			json_return_and_die(['error' => 'access denied']);
		}

		$data = $this->getStats();
		json_return_and_die($data);
	}

	private function getStats(): array {
		$stats = [];

		if (function_exists('sys_getloadavg')) {
			$stats['loadavg'] = sys_getloadavg();
		}

		$stats['dbqueries'] = $this->getNumQueries();
		$stats['outqueue'] = Queue::count();

		$qwstats = new QueueWorkerStats();
		$stats['queueworkers'] = $qwstats->active;
		$stats['workqsz'] = $qwstats->size;

		$profiler_state = SystemProfiler::isEnabled();
		$stats['profiler'] = [
			'status' => $profiler_state,
			'new_state' => [
				'action' => $profiler_state ? 'disable_profiling' : 'enable_profiling',
				'label' => $profiler_state ? t('Disable') : t('Enable'),
			],
		];

		// Return a timestamp, so that it is possible to infer
		// changes of the stats over time. A resolution of
		// seconds should be good enough for our purposes.
		$stats['ts'] = time();

		return $stats;
	}

	private function getNumQueries(): int {
		$stats = DbStats::getStats();
		return $stats->getQueries();
	}
}

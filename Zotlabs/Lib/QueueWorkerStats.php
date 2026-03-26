<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

class QueueWorkerStats
{
	public readonly int $size;
	public readonly int $active;

	public function __construct() {
		$query = <<<'SQL'
			select count(*) as total from workerq
			union (select count(*) as qworkers from workerq where workerq_reservationid is not null)
			SQL;

		$result = dbq('select count(*) as total from workerq');
		$this->size = !empty($result) ? $result[0]['total'] : -1;

		$result = dbq('select count(*) as qworkers from workerq where workerq_reservationid is not null');
		$this->active = !empty($result) ? $result[0]['qworkers'] : -1;
	}
}

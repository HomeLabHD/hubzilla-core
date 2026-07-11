<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Entity;

use DBA;
use Zotlabs\Lib\BaseObject;

class Abook extends BaseObject
{
	/**
	 * Return the number of contacts for a given channel.
	 *
	 * @param int $channelId
	 *     The numeric ID of the channel to look up.
	 *
	 * @return int
	 *     The numner of contacts of the given channel, or 0 in case of an
	 *     error.
	 */
	public static function countContactsForChannel(int $channelId): int
	{
		$r = q("select count(*) as total from abook where abook_channel = %d and abook_self = 0",
			$channelId
		);

		if ($r === false) {
			$err = DBA::$dba->db->errorInfo();
			logger("database error: {$err[0]} ({$err[1]}) {$err[2]}", LOGGER_NORMAL, LOG_ERR);
			return 0;
		}

		return intval($r[0]['total']);
	}

	/**
	 * Return the number of feeds for a given account.
	 *
	 * @param int $accountId
	 *     The numeric ID of the account to look up.
	 *
	 * @return int
	 *     The numner of feeds of the given account, or 0 in case of an
	 *     error.
	 */
	public static function countFeedsForAccount(int $accountId): int
	{
		$t = q("select count(*) as total from abook where abook_account = %d and abook_feed = 1",
			$accountId
		);

		if ($t === false) {
			$err = DBA::$dba->db->errorInfo();
			logger("database error: {$err[0]} ({$err[1]}) {$err[2]}", LOGGER_NORMAL, LOG_ERR);
			return 0;
		}

		return intval($t[0]['total']);
	}
}

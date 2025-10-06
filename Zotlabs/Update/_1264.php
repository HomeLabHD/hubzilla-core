<?php

namespace Zotlabs\Update;

use Zotlabs\Lib\Config;

class _1264 {

	function run() {

		dbq("START TRANSACTION");

		$admin_email = trim(Config::Get('system','admin_email'));

		$r = q("UPDATE account SET account_roles = 0 WHERE account_created > '2025-06-24 00:00:00' AND account_email <> '%s'",
			dbesc($admin_email)
		);

		if($r) {
			dbq("COMMIT");
			return UPDATE_SUCCESS;
		}

		dbq("ROLLBACK");
		return UPDATE_FAILED;

	}

}

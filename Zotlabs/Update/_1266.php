<?php

namespace Zotlabs\Update;

class _1266 {

	function run() {

		q("START TRANSACTION");

		if(ACTIVE_DBTYPE == DBTYPE_POSTGRES) {
			$r = dbq("create index xchan_updated on xchan (xchan_updated)");
		}
		else {
			$r = dbq("ALTER TABLE xchan ADD INDEX xchan_updated (xchan_updated)");
		}

		if($r) {
			q("COMMIT");
			return UPDATE_SUCCESS;
		}

		q("ROLLBACK");
		return UPDATE_FAILED;

	}

}

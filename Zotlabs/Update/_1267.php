<?php

namespace Zotlabs\Update;

class _1267 {

	function run() {

		q("START TRANSACTION");

		if(ACTIVE_DBTYPE == DBTYPE_POSTGRES) {
			$r = dbq("CREATE INDEX item_source_xchan ON item (source_xchan)");
		}
		else {
			$r = dbq("ALTER TABLE item ADD INDEX source_xchan (source_xchan)");
		}

		if($r) {
			q("COMMIT");
			return UPDATE_SUCCESS;
		}

		q("ROLLBACK");
		return UPDATE_FAILED;

	}

}

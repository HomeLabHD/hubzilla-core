<?php

namespace Zotlabs\Update;

class _1265 {

	function run() {

		dbq("START TRANSACTION");


		if(ACTIVE_DBTYPE == DBTYPE_POSTGRES) {
			$r = dbq("ALTER TABLE attach ALTER COLUMN filetype TYPE VARCHAR(128), ALTER COLUMN filetype SET NOT NULL, ALTER COLUMN filetype SET DEFAULT ''");
		}

		if(ACTIVE_DBTYPE == DBTYPE_MYSQL) {
			$r = dbq("ALTER TABLE attach CHANGE filetype filetype CHAR(128) NOT NULL DEFAULT ''");
		}

		if($r) {
			dbq("COMMIT");
			return UPDATE_SUCCESS;
		}

		q("ROLLBACK");
		return UPDATE_FAILED;

	}

}

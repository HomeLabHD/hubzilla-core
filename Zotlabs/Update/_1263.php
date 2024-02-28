<?php

namespace Zotlabs\Update;

class _1263 {

	function run() {

		dbq("START TRANSACTION");

		$poke = hash('whirlpool', 'Poke');
		$mood = hash('whirlpool', 'Mood');

		$r = dbq("DELETE FROM app WHERE (app_id = '$poke' OR app_id = '$mood') AND app_system = 1");

		if($r) {
			dbq("COMMIT");
			return UPDATE_SUCCESS;
		}

		dbq("ROLLBACK");
		return UPDATE_FAILED;

	}

}

<?php

namespace Zotlabs\Update;

use Zotlabs\Lib\Config;

class _1242 {

	function run() {
		$p = dbq("SELECT * FROM pconfig WHERE k LIKE '%password%'");
		foreach ($p as $pp) {
			if ($pp['v'][0] === '{') {
				$a = json_decode($pp['v'], true);
				if (isset($a['encrypted'])) {
					$v = crypto_unencapsulate($a, Config::Get('system', 'prvkey'));
					set_pconfig($pp['uid'], $pp['cat'], $pp['k'], obscurify($v));
				}
			}
		}
		return UPDATE_SUCCESS;
	}

}

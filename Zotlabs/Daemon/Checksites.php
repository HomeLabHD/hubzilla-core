<?php /** @file */

namespace Zotlabs\Daemon;

use Zotlabs\Lib\Config;

require_once('include/hubloc.php');

class Checksites {

	static public function run($argc, $argv) {

		logger('checksites: start');

		$site_id = '';
		$sql_options = '';

		if (($argc > 1) && ($argv[1]))
			$site_id = $argv[1];

		if ($site_id)
			$sql_options = " and site_url = '" . dbesc($argv[1]) . "' ";

		$days = intval(Config::Get('system', 'sitecheckdays'));
		if ($days < 1)
			$days = 30;

		$r = q("select * from site where site_dead = 0 and site_update < %s - INTERVAL %s $sql_options",
			db_utcnow(),
			db_quoteinterval($days . ' DAY')
		);

		if (!$r)
			return;

		foreach ($r as $rr) {
			if (!strcasecmp($rr['site_url'], z_root())) {
				continue;
			}

			$x = ping_site($rr['site_url'], $rr['site_type']);
			if ($x['success']) {
				logger('checksites: ' . $rr['site_url']);

				// We should not actually update the site type here as it should have been set correctly when the site was stored.
				// However, the site type was not always stored correctly for addon handlers like diaspora or pubcrawl.
				// ping_site() will now try to determine the correct type to fix the situation.
				// We might want to remove updating of site type here again after a while (maybe version 13).
				q("update site set site_update = '%s', site_type = %d, site_project = '%s', site_version = '%s' where site_url = '%s'",
					dbesc(datetime_convert()),
					intval($x['type']),
					dbesc($x['project']),
					dbesc($x['version']),
					dbesc($rr['site_url'])
				);
			}
			else {
				logger('marking dead site: ' . $x['message']);
				q("update site set site_dead = 1 where site_url = '%s' ",
					dbesc($rr['site_url'])
				);
			}
		}

		return;
	}
}

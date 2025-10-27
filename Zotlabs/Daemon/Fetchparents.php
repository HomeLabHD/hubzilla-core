<?php

namespace Zotlabs\Daemon;

use Zotlabs\Lib\Activity;

class Fetchparents {

	static public function run($argc, $argv) {

		logger('Fetchparents invoked: ' . print_r($argv, true));

		if ($argc < 4) {
			return;
		}

		$channels = explode(',', $argv[1]);
		if (!$channels) {
			return;
		}

		$observer_hash = $argv[2];
		if (!$observer_hash) {
			return;
		}

		$mid = $argv[3];
		if (!$mid) {
			return;
		}

		$force = $argv[4] ?? false;

		foreach ($channels as $channel_id) {
			$channel = channelx_by_n($channel_id);
			Activity::fetch_and_store_parents($channel, $observer_hash, $mid, null, $force);
		}

		Activity::init_background_fetch($observer_hash);

		return;

	}
}

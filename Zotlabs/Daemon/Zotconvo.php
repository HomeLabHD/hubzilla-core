<?php

namespace Zotlabs\Daemon;

use Zotlabs\Lib\Libzot;

class Zotconvo {

	static public function run($argc, $argv) {

		logger('Zotconvo invoked: ' . print_r($argv, true));

		if ($argc < 3) {
			return;
		}

		$channels = explode(',', $argv[1]);
		if (!$channels) {
			return;
		}

		$mid = $argv[2];
		if (!$mid) {
			return;
		}

		$force = $argv[3] ?? false;

		foreach ($channels as $channel_id) {
			$channel = channelx_by_n($channel_id);
			Libzot::fetch_conversation($channel, $mid, $force);
		}

		return;

	}
}

<?php

namespace Zotlabs\Daemon;

use Zotlabs\Lib\Activity;
use Zotlabs\Lib\ActivityStreams;
use Zotlabs\Lib\ASCollection;
use Zotlabs\Lib\ASCache;

class Convo {

	static public function run($argc, $argv) {

		logger('convo invoked: ' . print_r($argv, true));

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

			$obj = new ASCollection($mid, $channel);

			$messages = $obj->get();

			if (!$messages) {
				continue;
			}

			foreach ($messages as $message) {
				if (is_string($message)) {
					$cached = ASCache::Get($message);
					if ($cached) {
						// logger('convo_cached: ' . $message);
						$data = $cached;
					}
					else {
						// logger('convo_fetching: ' . $message);
						$data = Activity::fetch($message, $channel);
						if ($data) {
							ASCache::Set($message, $data);
						}
					}

				}
				else {
					$data = $message;
				}

				$AS = new ActivityStreams($data);
				if ($AS->is_valid() && is_array($AS->obj)) {
					$item = Activity::decode_note($AS);
					$item['item_fetched'] = true;
					Activity::store($channel, $observer_hash, $AS, $item, false, $force);
				}
			}

		}

		return;

	}
}

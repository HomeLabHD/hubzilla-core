<?php /** @file */

namespace Zotlabs\Daemon;

class Cache_embeds {

	static public function run($argc,$argv) {

		if(!$argc == 2) {
			return;
		}

		$c = q("select uid, aid, body, item_private from item where uuid = '%s'",
			dbesc($argv[1])
		);

		if(!$c) {
			return;
		}

		$item = $c[0];

		// bbcode conversion by default processes embeds that aren't already cached.
		// Ignore the returned html output.
		bbcode($item['body']);

		// photocache addon hook to prefetch one copy of public item images for the sys channel
		call_hooks('cache_prefetch_hook', $item);

		return;
	}

}

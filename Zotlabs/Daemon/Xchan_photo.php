<?php

/** @file */

namespace Zotlabs\Daemon;

class Xchan_photo {

	static public function run($argc, $argv) {

		if ($argc < 3) {
			return;
		}

		$url   = hex2bin($argv[1]);
		$xchan = hex2bin($argv[2]);
		$force = $argv[3];

		$photos = import_xchan_photo($url, $xchan, false, $force);
		if ($photos) {
			$result = q("update xchan set xchan_photo_date = '%s', xchan_photo_l = '%s', xchan_photo_m = '%s', xchan_photo_s = '%s', xchan_photo_mimetype = '%s' where xchan_hash = '%s'",
				dbescdate(datetime_convert()),
				dbesc($photos[0]),
				dbesc($photos[1]),
				dbesc($photos[2]),
				dbesc($photos[3]),
				dbesc($xchan)
			);

			if (! $result) {
				logger("xchan photo update failed for $url");
			}
		}

		return;
	}
}

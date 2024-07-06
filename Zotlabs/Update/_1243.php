<?php

namespace Zotlabs\Update;

use Zotlabs\Lib\Config;

class _1243 {

	function run() {

		$x = Config::Get('system','filesystem_storage_thumbnails');
		Config::Delete('system','filesystem_storage_thumbnails');
		if ($x !== false)
			Config::Set('system','photo_storage_type', intval($x));

		return UPDATE_SUCCESS;
	}

}

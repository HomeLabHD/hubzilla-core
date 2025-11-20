<?php

namespace Zotlabs\Lib;

class ObjCache
{
	public static function Get($path, $type = 'as')
	{
		if (!$path) {
			return [];
		}

		$localpath = Hashpath::path($path, 'store/[data]/[obj]/' . $type, 2, alg: 'sha256');
		if (file_exists($localpath)) {
			return unserialise(file_get_contents($localpath));
		}

		return [];
	}

	public static function Set($path, $content, $type = 'as') {
		if (!$path) {
			return;
		}

		$localpath = Hashpath::path($path, 'store/[data]/[obj]/' . $type, 2, alg: 'sha256');
		file_put_contents($localpath, serialise($content));
	}

	public static function Delete($path, $type = 'as') {
		if (!$path) {
			return;
		}

		$localpath = Hashpath::path($path, 'store/[data]/[obj]/' . $type, 2, alg: 'sha256');
		if (file_exists($localpath)) {
			unlink($localpath);
		}
	}
}

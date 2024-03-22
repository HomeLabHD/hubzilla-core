<?php /** @file */

namespace Zotlabs\Lib;

	/**
	 *  A wrapper for the cache api
	 */

class ASCache {
	public static function isEnabled() {
		return Config::Get('system', 'as_object_cache_enabled', true);
	}

	public static function getAge() {
		return Config::Get('system', 'as_object_cache_time', '10 MINUTE');
	}

	public static function Get($key) {
		if (!self::isEnabled()) {
			return;
		}

		return Cache::get($key, self::getAge());
	}

	public static function Set($key, $value) {
		if (!self::isEnabled()) {
			return;
		}

		Cache::set($key, $value);
	}
}

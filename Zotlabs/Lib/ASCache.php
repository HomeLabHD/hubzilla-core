<?php /** @file */

namespace Zotlabs\Lib;

	/**
	 *  A wrapper for the cache api
	 */

class ASCache {
	public static function isEnabled()
	{
		return Config::Get('system', 'as_object_cache_enabled', true);
	}

	public static function getAge(): string
	{
		return Config::Get('system', 'as_object_cache_time', '10 MINUTE');
	}

	public static function Get(string $key): array
	{
		if (!self::isEnabled()) {
			return [];
		}

		$ret = Cache::get($key, self::getAge());

		if ($ret) {
			return json_unserialize($ret);
		}

		return [];
	}

	public static function Set(string $key, array $obj): void
	{
		if (!self::isEnabled()) {
			return;
		}

		if (!self::isCacheable($obj)) {
			return;
		}

		Cache::set($key, json_serialize($obj));
	}

	public static function isCacheable(array $obj): bool
	{
		$to = [];
		$cc = [];

		if (isset($obj['to'])) {
			$to = is_array($obj['to']) ? $obj['to'] : [$obj['to']];
		}

		if (isset($obj['cc'])) {
			$cc = is_array($obj['cc']) ? $obj['cc'] : [$obj['cc']];
		}

		$receivers = array_merge($to, $cc);

		if ($receivers && !in_array(ACTIVITY_PUBLIC_INBOX, $receivers)) {
			return false;
		}

		return true;
	}
}

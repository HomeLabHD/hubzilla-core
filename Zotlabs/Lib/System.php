<?php

namespace Zotlabs\Lib;

class System
{
	private static ?string $platform_name = null;
	private static ?bool $hide_version = null;

	private static function init_platform_name(): void
	{
		if (self::$platform_name === null) {
			self::$platform_name = Config::Get('system', 'platform_name', '');
		}
	}

	private static function init_hide_version(): void
	{
		if (self::$hide_version === null) {
			self::$hide_version = (bool) Config::Get('system', 'hide_version');
		}
	}

	public static function get_platform_name(): string
	{
		self::init_platform_name();
		return self::$platform_name ?: PLATFORM_NAME;
	}

	public static function get_site_name(): string
	{
		return Config::Get('system', 'sitename', '');
	}

	public static function get_project_version(): string
	{
		self::init_hide_version();
		if (self::$hide_version) {
			return '';
		}

		$std_version = Config::Get('system', 'std_version', '');
		return $std_version ?: self::get_std_version();
	}

	public static function get_update_version(): string
	{
		self::init_hide_version();
		return self::$hide_version ? '' : DB_UPDATE_VERSION;
	}

	public static function get_notify_icon(): string
	{
		return Config::Get('system', 'email_notify_icon_url', '') ?: z_root() . DEFAULT_NOTIFY_ICON;
	}

	public static function get_site_icon(): string
	{
		return Config::Get('system', 'site_icon_url', '') ?: z_root() . DEFAULT_PLATFORM_ICON;
	}

	public static function get_project_link(): string
	{
		return Config::Get('system', 'project_link', '') ?: 'https://hubzilla.org';
	}

	public static function get_project_srclink(): string
	{
		return Config::Get('system', 'project_srclink', '') ?: 'https://framagit.org/hubzilla/core.git';
	}

	public static function get_server_role(): string
	{
		return 'pro';
	}

	public static function get_zot_revision(): string
	{
		$x = ['revision' => ZOT_REVISION];
		call_hooks('zot_revision', $x);
		return $x['revision'];
	}

	public static function get_std_version(): string
	{
		if (defined('STD_VERSION')) {
			return STD_VERSION;
		}

		return '0.0.0';
	}

	public static function compatible_project(string $p): bool
	{
		if (get_directory_realm() !== DIRECTORY_REALM) {
			return true;
		}

		$allowed = ['hubzilla', 'zap', 'red'];

		return in_array(strtolower($p), $allowed, true);
	}

	public static function get_useragent(): string
	{
		$useragent = Config::Get('system', 'site_useragent', '');
		if ($useragent !== '') {
			return $useragent;
		}

		$platform = self::get_platform_name();
		$version  = self::get_project_version();

		return ucfirst($platform) . '/' . $version . ' (+' . z_root() . ')';
	}
}

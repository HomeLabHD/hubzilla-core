<?php

namespace Zotlabs\Lib;

use App;

class System {

	static public function get_platform_name(): string
	{
		static $platform_name = '';
		if(empty($platform_name)) {
			if (isset(App::$config['system']['platform_name'])) {
				$platform_name = App::$config['system']['platform_name'];
			}
			else {
				$platform_name = PLATFORM_NAME;
			}
		}

		return $platform_name;
	}

	static public function get_site_name(): string
	{
		if (isset(App::$config['system']['sitename'])) {
			return App::$config['system']['sitename'];
		}

		return '';
	}

	static public function get_project_version(): string
	{
		if (isset(App::$config['system']['hide_version'])) {
			return '';
		}

		if (isset(App::$config['system']['std_version'])) {
			return App::$config['system']['std_version'];
		}

		return self::get_std_version();
	}

	static public function get_update_version(): string
	{
		if (isset(App::$config['system']['hide_version'])) {
			return '';
		}

		return DB_UPDATE_VERSION;
	}


	static public function get_notify_icon(): string
	{
		if (isset(App::$config['system']['email_notify_icon_url'])) {
			return App::$config['system']['email_notify_icon_url'];
		}

		return z_root() . DEFAULT_NOTIFY_ICON;
	}

	static public function get_site_icon(): string
	{
		if (isset(App::$config['system']['site_icon_url'])) {
			return App::$config['system']['site_icon_url'];
		}

		return z_root() . DEFAULT_PLATFORM_ICON ;
	}


	static public function get_project_link(): string
	{
		if (isset(App::$config['system']['project_link'])) {
			return App::$config['system']['project_link'];
		}

		return 'https://hubzilla.org';
	}

	static public function get_project_srclink(): string
	{
		if (isset(App::$config['system']['project_srclink'])) {
			return App::$config['system']['project_srclink'];
		}

		return 'https://framagit.org/hubzilla/core.git';
	}

	static public function get_server_role(): string
	{
		return 'pro';
	}


	static public function get_zot_revision(): string
	{
		$x = [ 'revision' => ZOT_REVISION ];
		call_hooks('zot_revision',$x);
		return $x['revision'];
	}

	static public function get_std_version(): string
	{
		if (defined('STD_VERSION')) {
			return STD_VERSION;
		}

		return '0.0.0';
	}

	static public function compatible_project($p): bool
	{

		if(get_directory_realm() != DIRECTORY_REALM) {
			return true;
		}

		if(in_array(strtolower($p),['hubzilla','zap','red'])) {
			return true;
		}

		return false;
	}

	static public function get_useragent(): string
	{
		if(isset(App::$config['system']['site_useragent'])) {
			return App::$config['system']['site_useragent'];
		}

		return ucfirst(self::get_platform_name()) . '/' . self::get_project_version() . ' (+' . z_root() . ')';
	}
}

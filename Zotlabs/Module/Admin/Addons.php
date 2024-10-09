<?php

namespace Zotlabs\Module\Admin;

use App;
use \Zotlabs\Lib\Config;
use \Michelf\MarkdownExtra;

class Addons {

	/**
	 * @brief
	 *
	 */
	function post() {

		if(argc() > 2 && is_file("addon/" . argv(2) . "/" . argv(2) . ".php")) {
			@include_once("addon/" . argv(2) . "/" . argv(2) . ".php");
			if(function_exists(argv(2).'_plugin_admin_post')) {
				$func = argv(2) . '_plugin_admin_post';
				$func();
			}

			goaway(z_root() . '/admin/addons/' . argv(2) );
		}
	}

	/**
	 * @brief Addons admin page.
	 *
	 * @return string with parsed HTML
	 */
	function get() {

		/*
		 * Single plugin
		 */

		if (App::$argc == 3){
			$plugin = App::$argv[2];
			if (!is_file("addon/$plugin/$plugin.php")){
				notice( t("Item not found.") );
				return '';
			}

			$enabled = in_array($plugin,App::$plugins);
			$info = get_plugin_info($plugin);
			$x = check_plugin_versions($info);

			// disable plugins which are installed but incompatible versions

			if($enabled && ! $x) {
				$enabled = false;
				$idz = array_search($plugin, App::$plugins);
				if ($idz !== false) {
					unset(App::$plugins[$idz]);
					uninstall_plugin($plugin);
					Config::Set("system","addon", implode(", ",App::$plugins));
				}
			}
			$info['disabled'] = 1-intval($x);

			if (x($_GET,"a") && $_GET['a']=="t"){
				check_form_security_token_redirectOnErr('/admin/addons', 'admin_addons', 't');
				$pinstalled = false;
				// Toggle plugin status
				$idx = array_search($plugin, App::$plugins);
				if ($idx !== false){
					unset(App::$plugins[$idx]);
					uninstall_plugin($plugin);
					$pinstalled = false;
					info( sprintf( t("Plugin %s disabled."), $plugin ) );
				} else {
					App::$plugins[] = $plugin;
					install_plugin($plugin);
					$pinstalled = true;
					info( sprintf( t("Plugin %s enabled."), $plugin ) );
				}
				Config::Set("system","addon", implode(", ",App::$plugins));

				if($pinstalled) {
					@require_once("addon/$plugin/$plugin.php");
					if(function_exists($plugin.'_plugin_admin'))
						goaway(z_root() . '/admin/addons/' . $plugin);
				}
				goaway(z_root() . '/admin/addons' );
			}

			// display plugin details

			if (in_array($plugin, App::$plugins)){
				$status = 'on';
				$action = t('Disable');
			} else {
				$status = 'off';
				$action =  t('Enable');
			}

			$readme = null;
			if (is_file("addon/$plugin/README.md")){
				$readme = file_get_contents("addon/$plugin/README.md");
				$readme = MarkdownExtra::defaultTransform($readme);
			} else if (is_file("addon/$plugin/README")){
				$readme = "<pre>". file_get_contents("addon/$plugin/README") ."</pre>";
			}

			$admin_form = '';

			$r = q("select * from addon where plugin_admin = 1 and aname = '%s' limit 1",
				dbesc($plugin)
			);

			if($r) {
				@require_once("addon/$plugin/$plugin.php");
				if(function_exists($plugin.'_plugin_admin')) {
					$func = $plugin.'_plugin_admin';
					$func($admin_form);
				}
			}


			$t = get_markup_template('admin_plugins_details.tpl');
			return replace_macros($t, array(
				'$title' => t('Administration'),
				'$page' => t('Addons'),
				'$toggle' => t('Toggle'),
				'$settings' => t('Settings'),
				'$baseurl' => z_root(),

				'$plugin' => $plugin,
				'$status' => $status,
				'$action' => $action,
				'$info' => $info,
				'$str_author' => t('Author: '),
				'$str_maintainer' => t('Maintainer: '),
				'$str_minversion' => t('Minimum project version: '),
				'$str_maxversion' => t('Maximum project version: '),
				'$str_minphpversion' => t('Minimum PHP version: '),
				'$str_serverroles' => t('Compatible Server Roles: '),
				'$str_requires' => t('Requires: '),
				'$disabled' => t('Disabled - version incompatibility'),

				'$admin_form' => $admin_form,
				'$function' => 'addons',
				'$screenshot' => '',
				'$readme' => $readme,

				'$form_security_token' => get_form_security_token('admin_addons'),
			));
		}


		/*
		 * List plugins
		 */
		$plugins = array();
		$files = glob('addon/*/');
		if($files) {
			foreach($files as $file) {
				if (is_dir($file)){
					if($file == 'addon/addon_common/')
						continue;

					list($tmp, $id) = array_map('trim', explode('/', $file));
					$info = get_plugin_info($id);
					$enabled = in_array($id,App::$plugins);
					$x = check_plugin_versions($info);

					// disable plugins which are installed but incompatible versions

					if($enabled && ! $x) {
						$enabled = false;
						$idz = array_search($id, App::$plugins);
						if ($idz !== false) {
							unset(App::$plugins[$idz]);
							uninstall_plugin($id);
							Config::Set("system","addon", implode(", ",App::$plugins));
						}
					}
					$info['disabled'] = 1-intval($x);

					$plugins[] = array( $id, (($enabled)?"on":"off") , $info);
				}
			}
		}

		usort($plugins,'self::plugin_sort');

		$t = get_markup_template('admin_plugins.tpl');
		return replace_macros($t, array(
			'$title' => t('Administration'),
			'$page' => t('Addons'),
			'$submit' => t('Submit'),
			'$baseurl' => z_root(),
			'$function' => 'addons',
			'$plugins' => $plugins,
			'$disabled' => t('Disabled - version incompatibility'),
			'$form_security_token' => get_form_security_token('admin_addons'),
		));
	}

	static public function plugin_sort($a,$b) {
		return(strcmp(strtolower($a[2]['name']),strtolower($b[2]['name'])));
	}

}

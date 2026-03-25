<?php
/*
 * SPDX-FileCopyrightText: 2018 The Hubzilla Community
 * SPDX-FileContributor: Zotlabs
 * SPDX-FileContributor: Mario <mario@mariovavti.com>
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Extend;

use Zotlabs\Lib\Config;

/**
 * Class for managing routes.
 *
 * Routes connect a URL path to a module that will handle requests to that
 * path.
 *
 * For example by registering a route like this:
 *
 * ```php
 * Route::register(
 *     __DIR__ . '/Mod_Myroute.php',
 *     'myroute'
 * );
 * ```
 *
 * Hubzilla will direct requests to the '/myroute' URL path to the 'Myroute'
 * controller located in the '/Mod_Myroute.php' file in the same directory as
 * the file this code was called from.
 *
 * Routes are stored persistently, so this function will typically be called from
 * the `<addon>_load()` function if called from an addon. Accordingly, the route must
 * be unregistered when no longer needed, like this:
 *
 * ```php
 * Route::unregister(
 *     __DIR__ . '/Mod_Myroute.php',
 *     'myroute'
 * );
 * ```
 *
 * This will typically be called from the `<addon>_unload()` function in an addon.
 */
class Route {

	/**
	 * Register a new route.
	 *
	 * Example:
	 * ```php
	 * Route::register(
	 *     __DIR__ . '/Mod_Myroute.php',
	 *     'myroute'
	 * );
	 * ```
	 *
	 * The route is stored persistently, and must be unregistered when no longer needed.
	 *
	 * @param string $file      The file containing the controller for handling requests to this route.
	 * @param string $modname   The name of the module (URL path).
	 *
	 * @see {@link Zotlabs::Extend::Route.unregister() unregister()}
	 * @see {@link Zotlabs::Extend::Route.unregister_by_file() unregister_by_file()}
	 */
	public static function register($file,$modname) {
		$rt = self::get();

		foreach ($rt as $r) {
			if ($r[0] === $file && $r[1] === $modname) {
				return;
			}
		}

		$rt[] = [ $file, $modname ];
		self::set($rt);
	}

	/**
	 * Unregister a route.
	 *
	 * Example:
	 * ```php
	 * Route::unregister(
	 *     __DIR__ . '/Mod_Myroute.php',
	 *     'myroute'
	 * );
	 * ```
	 *
	 * @param string $file      The file containing the controller for handling requests to this route.
	 * @param string $modname   The name of the module (URL path).
	 *
	 * @see {@link Zotlabs::Extend::Route.register() register()}
	 * @see {@link Zotlabs::Extend::Route.unregister_by_file() unregister_by_file()}
	 */
	public static function unregister($file,$modname) {
		$rt = self::get();
		if($rt) {
			$n = [];
			foreach($rt as $r) {
				if(!($r[0] === $file && $r[1] === $modname)) {
					$n[] = $r;
				}
			}
			self::set($n);
		}
	}

	/**
	 * Unregister all routes by source file.
	 *
	 * Removes all persistently stored routes with hanclers in the
	 * given source file.
	 *
	 * Example:
	 * ```php
	 * Route::unregister_by_file(__DIR__ . '/Mod_Myroute.php');
	 * ```
	 *
	 * @param string $file      The file containing the controllers to remove.
	 *
	 * @see {@link Zotlabs::Extend::Route.register() register()}
	 * @see {@link Zotlabs::Extend::Route.unregister() unregister()}
	 */
	public static function unregister_by_file($file) {
		$rt = self::get();
		if($rt) {
			$n = [];
			foreach($rt as $r) {
				if($r[0] !== $file) {
					$n[] = $r;
				}
			}
			self::set($n);
		}
	}

	/**
	 * Get an array of all defined routes.
	 *
	 * @return An array of routes, where each entry is an array
	 *               containing two elements, the file, and the module
	 *               name.
	 */
	public static function get() {
		return Config::Get('system','routes',[]);
	}

	private static function set($r) {
		return Config::Set('system','routes',$r);
	}
}


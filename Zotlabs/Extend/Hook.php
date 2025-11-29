<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: redmatrix
 * SPDX-FileContributor: Klaus Weidenbach
 * SPDX-FileContributor: zotlabs
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Extend;

use App;

/**
 * A class for hooking into Hubzilla.
 *
 * Hooks are functions that Hubzilla will invoke at certain points in the code
 * during execution. An addon can register a callback  handler that will be
 * called whenever the specified hook is invoked. A callback handler is a
 * function that takes a reference to an array containing the callback
 * arguments as it's only argument.
 *
 * @see call_hooks
 * @see load_hooks
 */
class Hook {

	/**
	 * Register a callback handler for a hook.
	 *
	 * A callback handler is a function that takes a reference to an array
	 * containing the callback arguments as it's only argument.
	 *
	 * The contents and meaning of the array depends on the hook invoked. By
	 * modifying the contents of the array the hook can pass data back to the
	 * caller.
	 *
	 * Once the `Hook::register` function has been called, the callback may be
	 * invoked.
	 *
	 * @param string $hook		The name of the hook to register a handler for.
	 * @param string $file		The source file of the callback handler.
	 * @param string|array $function
	 *                          The function name of the callback handler, as a
	 *                          string or an array.
	 * @param int $version		Hook interface version, allways 1.
	 * @param int $priority		The priority of the callback handler, higher
	 *                          numbers takes precedence.
	 *
	 * @return true if the handler was already registered, otherwise the result
	 *		from inserting the hook in the database.
	 */
	static public function register($hook,$file,$function,$version = 1,$priority = 0) {
		if(is_array($function)) {
			$function = serialize($function);
		}

		$r = q("SELECT * FROM hook WHERE hook = '%s' AND file = '%s' AND fn = '%s' and priority = %d and hook_version = %d LIMIT 1",
			dbesc($hook),
			dbesc($file),
			dbesc($function),
			intval($priority),
			intval($version)
		);
		if($r)
			return true;

		// To aid in upgrade and transition, remove old settings for any registered hooks that match in all respects except
		// for priority or hook_version

		$r = q("DELETE FROM hook where hook = '%s' and file = '%s' and fn = '%s'",
			dbesc($hook),
			dbesc($file),
			dbesc($function)
		);

		$r = q("INSERT INTO hook (hook, file, fn, priority, hook_version) VALUES ( '%s', '%s', '%s', %d, %d )",
			dbesc($hook),
			dbesc($file),
			dbesc($function),
			intval($priority),
			intval($version)
		);

		return $r;
	}

	/**
	 * Register an array of hook callback handlers.
	 *
	 * All of the handlers must be in the same source file.
	 *
	 * @param string $file	The source file of the callback handlers.
	 * @param array $arr	An array of `hookname => functionname` pairs.
	 */
	static public function register_array($file,$arr) {
		if($arr) {
			foreach($arr as $k => $v) {
				self::register($k,$file,$v);
			}
		}
	}


	/**
	 * Unregister a hook callback handler.
	 *
	 * @param string $hook		The name of the hook to register a callback handler for.
	 * @param string $file		The source file of the hook callback handler.
	 * @param string|array $function
	 *                          The function name of the callback handler, as a
	 *                          string or an array.
	 * @param int $version		Hook interface version, allways 1.
	 * @param int $priority		The priority of the callback handler, higher
	 *                          numbers takes precedence.
	 *
	 * @return The result of the database delete operation.
	 */
	static public function unregister($hook,$file,$function,$version = 1,$priority = 0) {
		if(is_array($function)) {
			$function = serialize($function);
		}
		$r = q("DELETE FROM hook WHERE hook = '%s' AND file = '%s' AND fn = '%s' and priority = %d and hook_version = %d",
			dbesc($hook),
			dbesc($file),
			dbesc($function),
			intval($priority),
			intval($version)
		);

		return $r;
	}

	/**
	 * Unregister all hooks handlers from a given source file.
	 *
	 * Useful for addon upgrades where you want to clean out old interfaces.
	 *
	 * @param string $file		The source file where the hook handlers were defined.
	 *
	 * @return The result from the database delete operation.
	 */
	static public function unregister_by_file($file) {
		$r = q("DELETE FROM hook WHERE file = '%s' ",
			dbesc($file)
		);

		return $r;
	}

	/**
	 * Inserts a hook into a page request.
	 *
	 * Insert a short-lived hook into the running page request. Hooks are
	 * normally persistent so that they can be called across asynchronous
	 * processes such as delivery and poll processes.
	 *
	 * This function lets you attach a hook callback immediately which will not
	 * persist beyond the life of this page request or the current process.
	 *
	 * @param string $hook		Name of hook to attach callback.
	 * @param string|array $fn	Name of callback handler as a string or array.
	 * @param int $version		Hook interface version, allways 1.
	 * @param int $priority		Currently not implemented in this function,
	 *                          would require the hook array to be resorted.
	 */
	static public function insert($hook, $fn, $version = 0, $priority = 0) {
		if(is_array($fn)) {
			$fn = serialize($fn);
		}

		if(! is_array(App::$hooks))
			App::$hooks = array();

		if(! array_key_exists($hook, App::$hooks))
			App::$hooks[$hook] = array();

		App::$hooks[$hook][] = array('', $fn, $priority, $version);
	}

}

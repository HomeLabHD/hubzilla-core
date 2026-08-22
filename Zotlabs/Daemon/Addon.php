<?php
/*
 * SPDX-FileCopyrightText: 2017 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Daemon;

/**
 * Run background hooks for addons.
 */
class Addon {

	/**
	 * Run background hooks for addons.
	 *
	 * This function will just invoke the `daemon_addon` hook with the
	 * arguments passed to it.
	 *
	 * Add a background task:
	 *
	 *     QueueWorker::Summon(['daemon_addon', 'myaddon', args...]);
	 *
	 * Where 'myaddon' is the name of your addon, or some other identifier that
	 * uniquely identifies your task.
	 *
	 * Add a hook to your addon:
	 *
	 *     function myaddon_load()
	 *     {
	 *         Hook::register('daemon_addon', __FILE__, 'myaddon_daemon_hook');
	 *     }
	 *
	 *     function myaddon_daemon_hook(array &$args): void
	 *     {
	 *	       if (empty($args) || $args[0] != 'daemon_addon' || $args[1] != 'myaddon') {
	 *             // Ignore invalid args
	 *	           return;
	 *	       }
	 *
	 *         // Process background task...
	 *     }
	 *
	 * Note that the hook function will _not_ be called from a web context, but the
	 * background QueueWorker process. This means that some of the normal superglobals
	 * may not be available, among other differences. See [the PHP documentation] for
	 * more information.
	 *
	 * [the PHP documentation]: https://www.php.net/manual/en/features.commandline.differences.php
	 */
	static public function run($argc, $argv) {

		/**
		 * @hooks daemon_addon Hook to allow addons to perform background tasks.
		 *     - \e array \b $argv - The args passed to the `Daemon::Summon()` call.
		 */
		call_hooks('daemon_addon', $argv);
		return;

	}

}

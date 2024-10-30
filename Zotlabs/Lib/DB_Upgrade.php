<?php
/**
 * A class to handle database schema upgrades.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use Zotlabs\Lib\Config;

/**
 * Upgrade the database schema if necessary.
 *
 * Compares the currently active database schema version with the version
 * required for this version of Hubzilla, and performs the upgrade if needed.
 *
 * If the difference consists of more than one revision of the schema, each of
 * the intermediate upgrades are performed in turn.
 */
class DB_Upgrade {

	/**
	 * Check the installed and required schema versions and perform the upgrade
	 * if necessary.
	 *
	 * @param int $db_version	The required DB schema version.
	 */
	public static function run(int $db_revision): void {

		$build = Config::Get('system', 'db_version', 0);
		if(! intval($build))
			$build = Config::Set('system', 'db_version', $db_revision);

		if($build == $db_revision) {
			// Nothing to be done.
			return;
		}
		else {
			$stored = intval($build);
			if(! $stored) {
				logger('Critical: check_config unable to determine database schema version');
				return;
			}

			$current = intval($db_revision);

			if($stored < $current) {

				// The last update we performed was $stored.
				// Start at $stored + 1 and continue until we have completed $current

				for($x = $stored + 1; $x <= $current; $x ++) {
					$s = '_' . $x;
					$cls = '\\Zotlabs\Update\\' . $s ;
					if(! class_exists($cls)) {
						return;
					}

					// There could be a lot of processes running or about to run.
					// We want exactly one process to run the update command.
					// So store the fact that we're taking responsibility
					// after first checking to see if somebody else already has.

					// If the update fails or times-out completely you may need to
					// delete the config entry to try again.

					Config::Load('database');

					if(Config::Get('database', $s))
						break;
					Config::Set('database',$s, '1');


					$c =  new $cls();

					$retval = $c->run();

					if($retval != UPDATE_SUCCESS) {


						$source = t('Source code of failed update: ') . "\n\n" . @file_get_contents('Zotlabs/Update/' . $s . '.php');


						// Prevent sending hundreds of thousands of emails by creating
						// a lockfile.

						$lockfile = 'store/[data]/mailsent';

						if ((file_exists($lockfile)) && (filemtime($lockfile) > (time() - 86400)))
							return;
						@unlink($lockfile);
						//send the administrator an e-mail
						file_put_contents($lockfile, $x);

						$r = q("select account_language from account where account_email = '%s' limit 1",
							dbesc(\App::$config['system']['admin_email'])
						);
						push_lang(($r) ? $r[0]['account_language'] : 'en');
						z_mail(
							[
								'toEmail'        => \App::$config['system']['admin_email'],
								'messageSubject' => sprintf( t('Update Error at %s'), z_root()),
								'textVersion'    => replace_macros(get_intltext_template('update_fail_eml.tpl'),
									[
										'$sitename' => \App::$config['system']['sitename'],
										'$siteurl' =>  z_root(),
										'$update' => $x,
										'$error' => sprintf( t('Update %s failed. See error logs.'), $x),
										'$baseurl' => z_root(),
										'$source' => $source
									]
								)
							]
						);

						//try the logger
						logger('CRITICAL: Update Failed: ' . $x);
						pop_lang();
					}
					else {
						Config::Set('database',$s, 'success');
					}
				}
			}
			Config::Set('system', 'db_version', $db_revision);
		}
	}
}

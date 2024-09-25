<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Lib\Apps;
use Zotlabs\Lib\Config;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\Enotify;
use Zotlabs\Lib\XConfig;

class Sse extends Controller {

	public static $uid;
	public static $ob_hash;
	public static $sse_id;
	public static $vnotify;
	public static $sse_enabled;

	function init() {

		// This is important!
		session_write_close();
		ignore_user_abort(true);

		if((observer_prohibited(true))) {
			killme();
		}

		self::$uid = local_channel();
		self::$ob_hash = get_observer_hash();
		self::$sse_id = false;
		self::$vnotify = -1;

		if (!self::$ob_hash) {
			if(session_id()) {
				self::$sse_id = true;
				self::$ob_hash = 'sse_id.' . session_id();
			}
			else {
				return;
			}
		}

		if (self::$uid) {
			self::$vnotify = get_pconfig(self::$uid, 'system', 'vnotify');
		}

		$sleep = 1000000; // microseconds

		self::$sse_enabled = Config::Get('system', 'sse_enabled', 0);

		if (self::$sse_enabled) {

			// Server Sent Events

			header("Content-Type: text/event-stream");
			header("Cache-Control: no-cache");
			header("Connection: keep-alive");
			header("X-Accel-Buffering: no");

			$i = 0;

			while(true) {

				// reset counter for updating chatpresence about every minute
				if (($i * $sleep)/60 > 1000000) {
					$i = 0;
				}

				if (!self::$sse_id && $i === 0) {
					// Update chat presence indication about once per minute
					$r = q("select cp_id, cp_room from chatpresence where cp_xchan = '%s' and cp_client = '%s' and cp_room = 0 limit 1",
						dbesc(self::$ob_hash),
						dbesc($_SERVER['REMOTE_ADDR'])
					);

					$basic_presence = false;

					if ($r) {
						$basic_presence = true;
						q("update chatpresence set cp_last = '%s' where cp_id = %d",
							dbesc(datetime_convert()),
							intval($r[0]['cp_id'])
						);
					}

					if (!$basic_presence) {
						q("insert into chatpresence ( cp_xchan, cp_last, cp_status, cp_client)
							values( '%s', '%s', '%s', '%s' ) ",
							dbesc(self::$ob_hash),
							dbesc(datetime_convert()),
							dbesc('online'),
							dbesc($_SERVER['REMOTE_ADDR'])
						);
					}
				}

				$result = [];

				XConfig::Load(self::$ob_hash);

				$lock = XConfig::Get(self::$ob_hash, 'sse', 'lock');

				if (!$lock) {
					$result = XConfig::Get(self::$ob_hash, 'sse', 'notifications', []);
				}

				// We do not have the local_channel in the addon.
				// Reset pubs here if the app is not installed.
				if (self::$uid && (!(self::$vnotify & VNOTIFY_PUBS) || !Apps::system_app_installed(self::$uid, 'Public Stream'))) {
					if (isset($result['pubs'])) {
						unset($result['pubs']);
					}
				}

				if ($result) {
					echo "event: notifications\n";
					echo 'data: ' . json_encode($result);
					echo "\n\n";
				}
				else {
					// if no result we will send a heartbeat to keep connected
					echo "event: heartbeat\n";
					echo 'data: {}';
					echo "\n\n";
				}

				if (connection_status() != CONNECTION_NORMAL || connection_aborted()) {

					// In case session_write_close() failed for some reason and
					// the channel was changed we might need to reset the
					// session to it's current stored state here.
					// Otherwise the uid might switch back to the previous value
					// in the background.

					session_reset();

					XConfig::Set(self::$ob_hash, 'sse', 'timestamp', NULL_DATE);
					XConfig::Set(self::$ob_hash, 'sse', 'notifications', []);

					if (ob_get_length() > 0) {
						ob_end_flush();
					}

					flush();

					exit;
				}

				if (ob_get_length() > 0) {
					ob_flush();
				}

				flush();

				usleep($sleep);

				if ($result) {
					XConfig::Set(self::$ob_hash, 'sse', 'notifications', []);
				}

				$i++;

			}

		}
		else {
			// Fallback to traditional polling

			if(!self::$sse_id) {

				// Update chat presence indication

				$r = q("select cp_id, cp_room from chatpresence where cp_xchan = '%s' and cp_client = '%s' and cp_room = 0 limit 1",
					dbesc(self::$ob_hash),
					dbesc($_SERVER['REMOTE_ADDR'])
				);
				$basic_presence = false;
				if ($r) {
					$basic_presence = true;
					q("update chatpresence set cp_last = '%s' where cp_id = %d",
						dbesc(datetime_convert()),
						intval($r[0]['cp_id'])
					);
				}
				if (!$basic_presence) {
					q("insert into chatpresence ( cp_xchan, cp_last, cp_status, cp_client)
						values( '%s', '%s', '%s', '%s' ) ",
						dbesc(self::$ob_hash),
						dbesc(datetime_convert()),
						dbesc('online'),
						dbesc($_SERVER['REMOTE_ADDR'])
					);
				}
			}

			$result = [];

			XConfig::Load(self::$ob_hash);

			$lock = XConfig::Get(self::$ob_hash, 'sse', 'lock');

			if (!$lock) {
				$result = XConfig::Get(self::$ob_hash, 'sse', 'notifications', []);
			}

			if ($result) {
				XConfig::Set(self::$ob_hash, 'sse', 'notifications', []);
				json_return_and_die($result);
			}

			killme();

		}

	}

}

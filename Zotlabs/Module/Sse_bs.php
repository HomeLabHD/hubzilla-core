<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Lib\Apps;
use Zotlabs\Lib\Config;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\Enotify;
use Zotlabs\Lib\XConfig;
use Zotlabs\Lib\Cache;

class Sse_bs extends Controller {

	public static $uid;
	public static $ob_hash;
	public static $sse_id;
	public static $vnotify;
	public static $evdays;
	public static $limit;
	public static $offset;
	public static $xchans;
	public static $direction;
	public static $count_limit;

	function init() {
		self::$uid = local_channel();
		self::$ob_hash = get_observer_hash();
		self::$sse_id = false;

		if(! self::$ob_hash) {
			if(session_id()) {
				self::$sse_id = true;
				self::$ob_hash = 'sse_id.' . session_id();
			}
			else {
				return;
			}
		}

		self::$vnotify = get_pconfig(self::$uid, 'system', 'vnotify', -1);
		self::$evdays = intval(get_pconfig(self::$uid, 'system', 'evdays'));
		self::$limit = 30;
		self::$offset = 0;
		self::$xchans = '';
		self::$direction = get_pconfig(self::$uid, 'system', 'invert_notifications_order', false) ? 'ASC' : 'DESC';
		self::$count_limit = get_pconfig(self::$uid, 'system', 'notifications_count_limit', 100);

		if (!empty($_REQUEST['sse_rmids'])) {
			self::mark_read(explode(',', $_REQUEST['sse_rmids']));
		}

		if(!empty($_REQUEST['nquery']) && $_REQUEST['nquery'] !== '%') {
			$nquery = $_REQUEST['nquery'];

			$x = q("SELECT xchan_hash FROM xchan WHERE xchan_name LIKE '%s' OR xchan_addr LIKE '%s'",
				dbesc($nquery . '%'),
				dbesc($nquery . '%')
			);

			self::$xchans = ids_to_querystr($x, 'xchan_hash', true);
		}

		if(intval(argv(2)) > 0) {
			self::$offset = argv(2);
		}
		else {
			$_SESSION['sse_loadtime'] = datetime_convert();
		}

		$network = false;
		$dm = false;
		$home = false;
		$pubs = false;
		$f = '';

		switch (argv(1)) {
			case 'network':
				$network = true;
				$f = 'bs_network';
				break;
			case 'dm':
				$dm = true;
				$f = 'bs_dm';
				break;
			case 'home':
				$home = true;
				$f = 'bs_home';
				break;
			case 'pubs':
				$pubs = true;
				$f = 'bs_pubs';
				break;
			default:
		}

		$selected_forum_id = null;
		if (str_starts_with(argv(1), 'forum_')) {
			$selected_forum_id = argv(1);
			$f = 'bs_forums';
		}

		if(self::$offset && $f) {
			$result = self::$f($selected_forum_id ?? true);
			json_return_and_die($result);
		}

		$result = array_merge(
			self::bs_network($network),
			self::bs_dm($dm),
			self::bs_home($home),
			self::bs_notify(),
			self::bs_intros(),
			self::bs_forums($selected_forum_id),
			self::bs_pubs($pubs),
			self::bs_files(),
			self::bs_all_events(),
			self::bs_register(),
			self::bs_info_notice()
		);

		XConfig::Set(self::$ob_hash, 'sse', 'notifications', []);
		XConfig::Set(self::$ob_hash, 'sse', 'timestamp', datetime_convert());
		XConfig::Set(self::$ob_hash, 'sse', 'language', App::$language);

		json_return_and_die($result);
	}

	function mark_read($arr) {
		$mids = [];
		$str = '';
		$slice = 0;

		$mids_all = isset($_SESSION['sse_mids_all']) ? json_unserialize($_SESSION['sse_mids_all']) : [];

		if (count($mids_all) > 3000) {
			$slice = count($mids_all) - 3000;
		}

		if ($slice) {
			$mids_all = array_slice($mids_all, $slice);
		}

		foreach($arr as $a) {
			$mid_str = '\'' . dbesc(unpack_link_id($a)) . '\'';
			$mids[] = $mid_str;

			if (!in_array($mid_str, $mids_all)) {
				$mids_all[] = $mid_str;
			}
		}

		$str = implode(',', $mids);

		$sys = get_sys_channel();
		$sql_order = ((self::$uid > $sys['channel_id']) ? 'DESC' : 'ASC');

		$r = q("SELECT uid, uuid FROM item
			WHERE uid in (%d, %d)
			AND verb IN ('Like', 'Dislike', 'Announce', 'Accept', 'Reject', 'TentativeAccept')
			AND thr_parent IN (
				SELECT mid FROM item WHERE uid IN (%d, %d) AND uuid IN (%s) ORDER BY uid $sql_order
			)
			GROUP BY uid, uuid
			ORDER BY uid $sql_order",
			intval(self::$uid),
			intval($sys['channel_id']),
			intval(self::$uid),
			intval($sys['channel_id']),
			$str // this is dbesc() in the above foreach loop
		);

		if ($r) {
			$activities_str = ids_to_querystr($r, 'uuid', true);
			$str .= ',' . $activities_str;
			$activities_arr = explode(',', $activities_str);
			$mids_all = array_merge($mids_all, $activities_arr);
		}

		$_SESSION['sse_mids_all'] = json_serialize(array_unique($mids_all));

		if(! self::$uid) {
			return;
		}

		$x = [ 'channel_id' => self::$uid, 'update' => 'unset' ];
		call_hooks('update_unseen',$x);

		if ($x['update'] === 'unset' || intval($x['update'])) {
			q("UPDATE item SET item_unseen = 0
				WHERE uid = %d
				AND uuid in (%s)
				AND item_unseen = 1",
				intval(self::$uid),
				$str // this is dbesc() in the above foreach loop
			);
		}
	}

	function bs_network($notifications) {

		$result['network']['notifications'] = [];
		$result['network']['count'] = 0;

		if(! self::$uid) {
			$result['network']['offset'] = -1;
			return $result;
		}

		if(! (self::$vnotify & VNOTIFY_NETWORK)) {
			$result['network']['offset'] = -1;
			return $result;
		}

		$limit = intval(self::$limit);
		$offset = self::$offset;
		$direction = self::$direction;
		$count_limit = intval(self::$count_limit);

		$sql_extra = '';
		if (!(self::$vnotify & VNOTIFY_LIKE)) {
			$sql_extra = " AND item.verb NOT IN ('Like', 'Dislike', '" . dbesc(ACTIVITY_LIKE) . "', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}
		elseif (!feature_enabled(self::$uid, 'dislike')) {
			$sql_extra = " AND item.verb NOT IN ('Dislike', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}

		$sql_extra2 = '';
		if(self::$xchans)
			$sql_extra2 = " AND CASE WHEN item.verb = '" . dbesc(ACTIVITY_SHARE) . "' THEN item.owner_xchan ELSE item.author_xchan END IN (" . self::$xchans . ") ";

		$item_normal = item_normal();

		// Filter internal follow activities and strerams add/remove activities
		$item_normal .= " AND item.verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

		if ($notifications) {
			$items = q("SELECT item.*, tp.uuid AS thr_parent_uuid FROM item
				LEFT JOIN item tp ON item.thr_parent = tp.mid AND item.uid = tp.uid
				WHERE item.uid = %d
				AND item.created <= '%s'
				AND item.item_unseen = 1 AND item.item_wall = 0 AND item.item_private IN (0, 1)
				AND item.obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
				AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity and not the resulting item
				AND NOT item.author_xchan = '%s'
				$item_normal
				$sql_extra
				$sql_extra2
				ORDER BY item.created $direction LIMIT $limit OFFSET $offset",
				intval(self::$uid),
				dbescdate($_SESSION['sse_loadtime']),
				dbesc(self::$ob_hash)
			);

			if($items) {
				call_hooks('sse_filter_items', [ 'uid' => self::$uid, 'items' => $items ]);

				$result['network']['offset'] = ((count($items) == $limit) ? intval($offset + $limit) : -1);
				xchan_query($items);
				foreach($items as $item) {
					$parsed = Enotify::format($item);
					if($parsed) {
						$result['network']['notifications'][] = $parsed;
					}
				}

				$result['network']['count'] = count($result['network']['notifications']);
			}
			else {
				$result['network']['offset'] = -1;
			}

			return $result;
		}

		$r = q("SELECT id FROM item
			WHERE uid = %d and item_unseen = 1 AND item_wall = 0 AND item_private IN (0, 1)
			AND obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
			AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity and not the resulting item
			AND author_xchan != '%s'
			$item_normal
			$sql_extra LIMIT $count_limit",
			intval(self::$uid),
			dbesc(self::$ob_hash)
		);

		if($r)
			$result['network']['count'] = count($r);

		return $result;
	}

	function bs_dm($notifications) {

		$result['dm']['notifications'] = [];
		$result['dm']['count'] = 0;

		if(! self::$uid) {
			$result['dm']['offset'] = -1;
			return $result;
		}

		if(! (self::$vnotify & VNOTIFY_MAIL)) {
			$result['dm']['offset'] = -1;
			return $result;
		}

		$limit = intval(self::$limit);
		$offset = self::$offset;
		$direction = self::$direction;
		$count_limit = intval(self::$count_limit);

		$sql_extra = '';
		if (!(self::$vnotify & VNOTIFY_LIKE)) {
			$sql_extra = " AND item.verb NOT IN ('Like', 'Dislike', '" . dbesc(ACTIVITY_LIKE) . "', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}
		elseif (!feature_enabled(self::$uid, 'dislike')) {
			$sql_extra = " AND item.verb NOT IN ('Dislike', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}

		$sql_extra2 = '';
		if(self::$xchans)
			$sql_extra2 = " AND CASE WHEN item.verb = '" . ACTIVITY_SHARE . "' THEN item.owner_xchan ELSE item.author_xchan END IN (" . self::$xchans . ") ";

		$item_normal = item_normal();

		// Filter internal follow activities and strerams add/remove activities
		$item_normal .= " AND item.verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

		if ($notifications) {
			$items = q("SELECT item.*, tp.uuid AS thr_parent_uuid FROM item
				LEFT JOIN item tp ON item.thr_parent = tp.mid AND item.uid = tp.uid
				WHERE item.uid = %d
				AND item.created <= '%s'
				AND item.item_unseen = 1 AND item.item_private = 2
				AND item.obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
				AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity and not the resulting item
				AND NOT item.author_xchan = '%s'
				$item_normal
				$sql_extra
				$sql_extra2
				ORDER BY created $direction LIMIT $limit OFFSET $offset",
				intval(self::$uid),
				dbescdate($_SESSION['sse_loadtime']),
				dbesc(self::$ob_hash)
			);

			if($items) {
				$result['dm']['offset'] = ((count($items) == $limit) ? intval($offset + $limit) : -1);
				xchan_query($items);
				foreach($items as $item) {
					$parsed = Enotify::format($item);
					if($parsed) {
						$result['dm']['notifications'][] = $parsed;
					}
				}
			}
			else {
				$result['dm']['offset'] = -1;
			}

		}

		$r = q("SELECT id FROM item
			WHERE uid = %d and item_unseen = 1 AND item_private = 2
			$item_normal
			$sql_extra
			AND author_xchan != '%s' LIMIT $count_limit",
			intval(self::$uid),
			dbesc(self::$ob_hash)
		);

		if($r)
			$result['dm']['count'] = count($r);

		return $result;
	}

	function bs_home($notifications) {

		$result['home']['notifications'] = [];
		$result['home']['count'] = 0;

		if(! self::$uid) {
			$result['home']['offset'] = -1;
			return $result;
		}

		if(! (self::$vnotify & VNOTIFY_CHANNEL)) {
			$result['home']['offset'] = -1;
			return $result;
		}

		$limit = intval(self::$limit);
		$offset = self::$offset;
		$direction = self::$direction;
		$count_limit = intval(self::$count_limit);

		$sql_extra = '';
		if (!(self::$vnotify & VNOTIFY_LIKE)) {
			$sql_extra = " AND item.verb NOT IN ('Like', 'Dislike', '" . dbesc(ACTIVITY_LIKE) . "', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}
		elseif (!feature_enabled(self::$uid, 'dislike')) {
			$sql_extra = " AND item.verb NOT IN ('Dislike', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}

		$sql_extra2 = '';
		if(self::$xchans)
			$sql_extra2 = " AND CASE WHEN item.verb = '" . ACTIVITY_SHARE . "' THEN item.owner_xchan ELSE item.author_xchan END IN (" . self::$xchans . ") ";


		$item_normal = item_normal();

		// Filter internal follow activities and strerams add/remove activities
		$item_normal .= " AND item.verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

		if ($notifications) {
			$items = q("SELECT item.*, tp.uuid AS thr_parent_uuid FROM item
				LEFT JOIN item tp ON item.thr_parent = tp.mid AND item.uid = tp.uid
				WHERE item.uid = %d
				AND item.created <= '%s'
				AND item.item_unseen = 1 AND item.item_wall = 1 AND item.item_private IN (0, 1)
				AND item.obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
				AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity and not the resulting item
				AND NOT item.author_xchan = '%s'
				$item_normal
				$sql_extra
				$sql_extra2
				ORDER BY item.created $direction LIMIT $limit OFFSET $offset",
				intval(self::$uid),
				dbescdate($_SESSION['sse_loadtime']),
				dbesc(self::$ob_hash)
			);

			if($items) {
				$result['home']['offset'] = ((count($items) == $limit) ? intval($offset + $limit) : -1);
				xchan_query($items);
				foreach($items as $item) {
					$parsed = Enotify::format($item);
					if($parsed) {
						$result['home']['notifications'][] = $parsed;
					}
				}
			}
			else {
				$result['home']['offset'] = -1;
			}

		}

		$r = q("SELECT id FROM item
			WHERE uid = %d and item_unseen = 1 AND item_wall = 1 AND item_private IN (0, 1)
			$item_normal
			$sql_extra
			AND author_xchan != '%s' LIMIT $count_limit",
			intval(self::$uid),
			dbesc(self::$ob_hash)
		);

		if($r)
			$result['home']['count'] = count($r);

		return $result;
	}

	function bs_pubs($notifications) {

		$result['pubs']['notifications'] = [];
		$result['pubs']['count'] = 0;

		if(! (self::$vnotify & VNOTIFY_PUBS) || !Apps::system_app_installed(self::$uid, 'Public Stream')) {
			$result['pubs']['offset'] = -1;
			return $result;
		}

		if((observer_prohibited(true))) {
			$result['pubs']['offset'] = -1;
			return $result;
		}

		if(! intval(Config::Get('system','open_pubstream',1))) {
			if(! get_observer_hash()) {
				$result['pubs']['offset'] = -1;
				return $result;
			}
		}

		if(!isset($_SESSION['static_loadtime'])) {
			$_SESSION['static_loadtime'] = datetime_convert();
		}

		$limit = intval(self::$limit);
		$offset = self::$offset;
		$direction = self::$direction;
		$count_limit = intval(self::$count_limit);

		$sys = get_sys_channel();
		$sql_extra = '';
		if (!(self::$vnotify & VNOTIFY_LIKE)) {
			$sql_extra = " AND item.verb NOT IN ('Like', 'Dislike', '" . dbesc(ACTIVITY_LIKE) . "', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}
		elseif (!feature_enabled(self::$uid, 'dislike')) {
			$sql_extra = " AND item.verb NOT IN ('Dislike', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
		}

		$sql_extra2 = '';
		if(self::$xchans)
			$sql_extra2 = " AND CASE WHEN item.verb = '" . ACTIVITY_SHARE . "' THEN item.owner_xchan ELSE item.author_xchan END IN (" . self::$xchans . ") ";

		$sql_extra3 = '';
		$sse_mids_all = isset($_SESSION['sse_mids_all']) ? json_unserialize($_SESSION['sse_mids_all']) : [];
		if ($sse_mids_all) {
			$sql_extra3 = " AND item.uuid NOT IN (" . protect_sprintf(implode(',', $sse_mids_all)) . ") ";
		}

		$uids = " AND item.uid IN ( " . $sys['channel_id'] . " ) ";

		$site_firehose = Config::Get('system', 'site_firehose', 0);
		if($site_firehose) {
			$uids = " AND item.uid IN ( " . stream_perms_api_uids(PERMS_PUBLIC) . " ) AND item.item_private = 0 AND item.item_wall = 1 ";
		}

		$item_normal = item_normal();

		// Filter internal follow activities and strerams add/remove activities
		$item_normal .= " AND item.verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

		if ($notifications) {
			$items = q("SELECT item.*, tp.uuid AS thr_parent_uuid FROM item
				LEFT JOIN item tp ON item.thr_parent = tp.mid AND item.uid = tp.uid
				WHERE true $uids
				AND item.created <= '%s'
				AND item.created > '%s'
				AND item.obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
				AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity not the resulting item
				AND NOT item.author_xchan = '%s'
				$item_normal
				$sql_extra
				$sql_extra2
				$sql_extra3
				ORDER BY item.created $direction LIMIT $limit OFFSET $offset",
				dbescdate($_SESSION['sse_loadtime']),
				dbescdate($_SESSION['static_loadtime']),
				dbesc(self::$ob_hash)
			);

			if($items) {
				$result['pubs']['offset'] = ((count($items) == $limit) ? intval($offset + $limit) : -1);
				xchan_query($items);
				foreach($items as $item) {
					$parsed = Enotify::format($item);
					if($parsed) {
						$result['pubs']['notifications'][] = $parsed;
					}
				}
			}
			else {
				$result['pubs']['offset'] = -1;
			}
		}

		$r = q("SELECT id, body FROM item
			WHERE true $uids
			AND created <= '%s'
			AND created > '%s'
			$item_normal
			$sql_extra
			$sql_extra3
			AND author_xchan != '%s' LIMIT $count_limit",
			dbescdate($_SESSION['sse_loadtime']),
			dbescdate($_SESSION['static_loadtime']),
			dbesc(self::$ob_hash)
		);

		if($r)
			$result['pubs']['count'] = count($r);

		return $result;
	}


	function bs_notify() {

		$result['notify']['notifications'] = [];
		$result['notify']['count'] = 0;
		$result['notify']['offset'] = -1;

		if(! self::$uid)
			return $result;

		if(! (self::$vnotify & VNOTIFY_SYSTEM))
			return $result;

		$direction = self::$direction;

		$r = q("SELECT * FROM notify WHERE uid = %d AND seen = 0 ORDER BY created $direction",
			intval(self::$uid)
		);

		if($r) {
			foreach($r as $rr) {
				$result['notify']['notifications'][] = Enotify::format_notify($rr);
			}
			$result['notify']['count'] = count($r);
		}

		return $result;

	}

	function bs_intros() {

		$result['intros']['notifications'] = [];
		$result['intros']['count'] = 0;
		$result['intros']['offset'] = -1;

		if(! self::$uid)
			return $result;

		if(! (self::$vnotify & VNOTIFY_INTRO))
			return $result;

		$direction = self::$direction;

		$r = q("SELECT * FROM abook left join xchan on abook.abook_xchan = xchan.xchan_hash where abook_channel = %d and abook_pending = 1 and abook_self = 0 and abook_ignored = 0 and xchan_deleted = 0 and xchan_orphan = 0 ORDER BY abook_created $direction LIMIT 50",
			intval(self::$uid)
		);

		if($r) {
			foreach($r as $rr) {
				$result['intros']['notifications'][] = Enotify::format_intros($rr);
			}
			$result['intros']['count'] = count($r);
		}

		return $result;

	}

	function bs_forums($selected_forum_id) {
		$forums = get_forum_channels(self::$uid);

		if(!self::$uid || !(self::$vnotify & VNOTIFY_FORUMS) || !$forums) {
			$result['forum']['notifications'] = [];
			$result['forum']['count'] = 0;
			$result['forum']['offset'] = -1;
			return $result;
		}

		if($forums) {
			$fcount = count($forums);

			for($x = 0; $x < $fcount; $x ++) {

				$forum_id = 'forum_' . $forums[$x]['abook_id'];

				$result[$forum_id]['notifications'] = [];
				$result[$forum_id]['count'] = 0;

				$limit = intval(self::$limit);
				$offset = self::$offset;
				$direction = self::$direction;
				$count_limit = intval(self::$count_limit);

				$sql_extra = '';
				if (!(self::$vnotify & VNOTIFY_LIKE)) {
					$sql_extra = " AND item.verb NOT IN ('Like', 'Dislike', '" . dbesc(ACTIVITY_LIKE) . "', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
				}
				elseif (!feature_enabled(self::$uid, 'dislike')) {
					$sql_extra = " AND item.verb NOT IN ('Dislike', '" . dbesc(ACTIVITY_DISLIKE) . "') ";
				}

				$item_normal = item_normal();

				// Filter internal follow activities and strerams add/remove activities
				$item_normal .= " AND item.verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

				if ($forum_id === $selected_forum_id) {
					$items = q("SELECT item.*, tp.uuid AS thr_parent_uuid FROM item
						LEFT JOIN item tp ON item.thr_parent = tp.mid AND item.uid = tp.uid
						WHERE item.uid = %d
						AND item.created <= '%s'
						AND item.owner_xchan = '%s'
						AND item.item_unseen = 1 AND item.item_wall = 0 AND item.item_private IN (0, 1)
						AND item.obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
						AND NOT (item.verb = 'Announce' AND item.item_thread_top = 1) -- only show the announce activity and not the resulting item
						AND NOT item.author_xchan = '%s'
						$item_normal
						$sql_extra
						ORDER BY item.created $direction LIMIT $limit OFFSET $offset",
						intval(self::$uid),
						dbescdate($_SESSION['sse_loadtime']),
						dbescdate($forums[$x]['xchan_hash']),
						dbesc(self::$ob_hash)
					);

					if ($items) {
						$result[$forum_id]['offset'] = ((count($items) == $limit) ? intval($offset + $limit) : -1);
						xchan_query($items);
						foreach($items as $item) {
							$parsed = Enotify::format($item);
							if($parsed) {
								$result[$forum_id]['notifications'][] = $parsed;
							}
						}
					}
					else {
						$result[$forum_id]['offset'] = -1;
					}

				}

				$r = q("SELECT id FROM item
					WHERE uid = %d and item_unseen = 1 AND item_wall = 0 AND item_private IN (0, 1)
					AND obj_type NOT IN ('Document', 'Video', 'Audio', 'Image')
					AND author_xchan != '%s'
					AND item.owner_xchan = '%s'
					$item_normal
					$sql_extra LIMIT $count_limit",
					intval(self::$uid),
					dbesc(self::$ob_hash),
					dbesc($forums[$x]['xchan_hash'])
				);

				if ($r) {
					$result[$forum_id]['count'] = count($r);
				}
			}
		}

		return $result;

	}

	function bs_files() {

		$result['files']['notifications'] = [];
		$result['files']['count'] = 0;
		$result['files']['offset'] = -1;

		if(! self::$uid)
			return $result;

		if(! (self::$vnotify & VNOTIFY_FILES))
			return $result;

		$direction = self::$direction;
		$item_normal = item_normal();

		// Filter internal follow activities and strerams add/remove activities
		$item_normal .= " AND verb NOT IN ('Add', 'Remove', 'Follow', 'Ignore', '" . dbesc(ACTIVITY_FOLLOW) . "') ";

		$r = q("SELECT * FROM item
			WHERE (verb = 'Create' OR verb = '%s')
			AND obj_type IN ('Document', 'Video', 'Audio', 'Image')
			AND uid = %d
			AND author_xchan != '%s'
			AND item_unseen = 1
			$item_normal
			ORDER BY created $direction",
			dbesc(ACTIVITY_POST),
			intval(self::$uid),
			dbesc(self::$ob_hash)
		);
		if($r) {
			xchan_query($r);
			foreach($r as $rr) {
					$parsed = Enotify::format($rr);
					if($parsed) {
						$result['files']['notifications'][] = $parsed;
					}
			}
			$result['files']['count'] = count($r);
		}

		return $result;

	}

	function bs_all_events() {

		$result['all_events']['notifications'] = [];
		$result['all_events']['count'] = 0;
		$result['all_events']['offset'] = -1;

		if(! self::$uid)
			return $result;

		if(! (self::$vnotify & VNOTIFY_EVENT))
			return $result;

		$direction = self::$direction;

		$r = q("SELECT * FROM event left join xchan on event_xchan = xchan_hash
			WHERE event.uid = %d AND dtstart < '%s' AND dtstart > '%s' and dismissed = 0
			and etype in ( 'event', 'birthday' )
			ORDER BY dtstart $direction",
			intval(self::$uid),
			dbesc(datetime_convert('UTC', date_default_timezone_get(), 'now + ' . intval(self::$evdays) . ' days')),
			dbesc(datetime_convert('UTC', date_default_timezone_get(), 'now - 1 days'))
		);

		if($r) {
			foreach($r as $rr) {
				$result['all_events']['notifications'][] = Enotify::format_all_events($rr);
			}
			$result['all_events']['count'] = count($r);
		}

		return $result;
	}

	function bs_register() {

		$result['register']['notifications'] = [];
		$result['register']['count'] = 0;
		$result['register']['offset'] = -1;

		if(! self::$uid && ! is_site_admin())
			return $result;

		$policy  = intval(Config::Get('system','register_policy'));
		if(($policy & REGISTER_APPROVE) != REGISTER_APPROVE)
			return $result;

		if(! (self::$vnotify & VNOTIFY_REGISTER))
			return $result;

		$r = get_pending_accounts();

		if($r) {
			foreach($r as $rr) {
				$result['register']['notifications'][] = Enotify::format_register($rr);
			}
			$result['register']['count'] = count($r);
		}

		return $result;

	}

	function bs_info_notice() {

		$result['notice']['notifications'] = [];
		$result['info']['notifications'] = [];

		$r = XConfig::Get(self::$ob_hash, 'sse', 'notifications', []);

		if(isset($r['notice']))
			$result['notice']['notifications'] = $r['notice']['notifications'];

		if(isset($r['info']))
			$result['info']['notifications'] = $r['info']['notifications'];

		return $result;

	}


}

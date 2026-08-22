<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\AccessList;
use Zotlabs\Lib\ActivityStreams;
use Zotlabs\Lib\Activity;
use Zotlabs\Web\HTTPSig;
use Zotlabs\Lib\Config;



class Lists extends Controller
{

	public function init()
	{
		if (!ActivityStreams::is_as_request()) {
			http_status_exit(400, 'Bad Request');
		}

		$item_id = argv(1);
		if (!$item_id) {
			http_status_exit(404, 'Not found');
		}

		$x = q(
			"select * from pgrp where hash = '%s' limit 1",
			dbesc($item_id)
		);

		if (!$x) {
			http_status_exit(404, 'Not found');
		}

		$group = array_shift($x);
		$channel = channelx_by_n($group['uid']);

		if (!$channel) {
			http_status_exit(404, 'Not found');
		}

		$sigdata = HTTPSig::verify(EMPTY_STR);
		if ($sigdata['portable_id'] && $sigdata['header_valid']) {
			$portable_id = $sigdata['portable_id'];
			if (!check_channelallowed($portable_id)) {
				http_status_exit(403, 'Permission denied');
			}
			if (!check_siteallowed($sigdata['signer'])) {
				http_status_exit(403, 'Permission denied');
			}
			observer_auth($portable_id);
		}

		$observer_hash = get_observer_hash();
		$has_permission = perm_is_allowed($channel['channel_id'], $observer_hash, 'view_contacts');

		if (!empty($group) && !$group['visible']) {
			$has_permission = false;
		}

		$sql_extra = '';
		if (!$has_permission) {
			if ($observer_hash) {
				if ($observer_hash !== $channel['channel_hash']) {
					$sql_extra = " AND xchan_hash = '" . dbesc($observer_hash) . "' ";
				}
			}
			else {
				http_status_exit(403, 'Permission denied');
			}
		}

		$total = AccessList::members($channel['channel_id'], $group['id'], true, sql_extra: $sql_extra);

		if ($total) {
			App::set_pager_total($total);
			App::set_pager_itemspage(30);
		}

		if (!$total && !$has_permission) {
			http_status_exit(403, 'Permission denied');
		}

		$ret = [];

		if (empty($_GET['page']) && $total > App::$pager['itemspage']) {
			$ret = Activity::paged_collection_init($total, App::$query_string, 'Collection', 'actor');
		} else {
			$members = AccessList::members($channel['channel_id'], $group['id'], false, App::$pager['start'], App::$pager['itemspage'], sql_extra: $sql_extra);
			$ret = Activity::encode_follow_collection($members, App::$query_string, 'Collection', $total);
		}

		if (!$sql_extra) {
			$ret['name'] = $group['gname'];
		}

		$ret['attributedTo'] = channel_url($channel);

		as_return_and_die($ret, $channel);
	}
}

<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\Activity;
use Zotlabs\Daemon\Master;

class React extends Controller {

	function get() {

		if (!local_channel()) {
			return;
		}

		$sys = get_sys_channel();
		$channel = App::get_channel();

		$postid = $_REQUEST['postid'];

		if (!$postid) {
			return;
		}

		$shortname = $_REQUEST['emoji'];

		$emojis = get_emojis();

		if (!isset($emojis[$shortname])) {
			return;
		}

		$emoji = $emojis[$shortname];

		if (!$emoji) {
			return;
		}

		$i = q("select * from item where id = %d and uid = %d",
			intval($postid),
			intval(local_channel())
		);

		if (!$i) {
			$i = q("select * from item where id = %d and uid = %d",
				intval($postid),
				intval($sys['channel_id'])
			);

			if ($i) {
				$i = [ copy_of_pubitem($channel, $i[0]['mid']) ];
				$postid = (($i) ? $i[0]['id'] : 0);
			}
		}

		if (!$i) {
			return;
		}

		$uuid = item_message_id();

		$n['aid'] = $channel['channel_account_id'];
		$n['uid'] = $channel['channel_id'];
		$n['item_origin'] = true;
		$n['item_type'] = $i[0]['item_type'];
		$n['parent'] = $postid;
		$n['parent_mid'] = $i[0]['mid'];
		$n['uuid'] = $uuid;
		$n['mid'] = z_root() . '/item/' . $uuid;
		$n['verb'] = 'Create';
		$n['body'] =  $emoji['shortname']; //'[img class="emoji single-emoji"]' . z_root() . '/' . $emoji['filepath'] . '[/img]';
		$n['author_xchan'] = $channel['channel_hash'];
	//	$n['obj'] = Activity::fetch_item(['id' => $i[0]['mid']]);
	//	$n['obj_type'] = ((array_path_exists('obj/type', $n)) ? $n['obj']['type'] : EMPTY_STR);

		$n['term'][] = [
			'uid'   => $channel['channel_id'],
			'ttype' => TERM_EMOJI,
			'otype' => TERM_OBJ_POST,
			'term'  => $emoji['shortname'],
			'url'   => z_root() . '/emoji/' . $shortname,
			'imgurl' => z_root() . '/' . $emoji['filepath']
		];

		$x = item_store($n);

		retain_item($postid);

		if ($x['success']) {
			$nid = $x['item_id'];
			Master::Summon(['Notifier', 'like', $nid]);
			if (!empty($x['approval_id'])) {
				Master::Summon(['Notifier', 'like', $x['approval_id']]);
			}
		}
	}

}

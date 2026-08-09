<?php
namespace Zotlabs\Module;

use App;
use Zotlabs\Lib\ASObjectStorage;
use Zotlabs\Web\Controller;
use Zotlabs\Lib\Activity;
use Zotlabs\Daemon\Master;
use Zotlabs\Lib\Libsync;

class Vote extends Controller {

	function init() {

		$ret = [ 'success' => false, 'message' => EMPTY_STR ];

		$channel = App::get_channel();

		if (! $channel) {
			$ret['message'] = t('Permission denied.');
			json_return_and_die($ret);
		}

		$id = argv(1);

		if (!$id) {
			$ret['message'] = t('Missing poll id.');
			json_return_and_die($ret);
		}

		$answer = q("select * from item where parent = %d and uid = %d and obj_type = 'Answer' limit 1",
			intval($id),
			intval($channel['channel_id'])
		);

		if ($answer) {
			$ret['message'] = t('You have already submitted your vote for this poll.');
			json_return_and_die($ret);
		}

		$poll = q("select * from item where id = %d limit 1",
			intval($id)
		);

		if (!$poll && $poll[0]['obj_type'] !== 'Question') {
			$ret['message'] = t('Poll not found.');
			json_return_and_die($ret);
		}

		$response = $_REQUEST['answer'];
		$obj = (new ASObjectStorage($poll[0]['obj']))->decode();
        if (!is_array($obj)) {
            $ret['message'] = t('Unable to de-reference object.');
            json_return_and_die($ret);
        }

		$valid = false;

		if ($obj['oneOf']) {
			foreach($obj['oneOf'] as $selection) {
				if($selection['name'] && htmlspecialchars_decode($selection['name']) === $response) {
					$valid = true;
				}
			}
		}

		$choices = [];
		if ($obj['anyOf']) {
			foreach ($obj['anyOf'] as $selection) {
				$choices[] = htmlspecialchars_decode($selection['name']);
			}
			foreach ($response as $res) {
				if (! in_array($res,$choices)) {
					$valid = false;
					break;
				}
				$valid = true;
			}
		}

		if (! $valid) {
			$ret['message'] = t('Invalid response.');
			json_return_and_die($ret);
		}

		if (! is_array($response)) {
			$response = [ $response ];
		}

		foreach ($response as $res) {

			$item = [];

			$item['aid'] = $channel['channel_account_id'];
			$item['uid'] = $channel['channel_id'];
			$item['item_origin'] = 1;
			$item['parent'] = $poll[0]['id'];
			$item['parent_mid'] = $poll[0]['mid'];
			$item['thr_parent'] = $poll[0]['mid'];
			$item['uuid'] = new_uuid();
			$item['mid'] = z_root() . '/item/' . $item['uuid'];
			$item['verb'] = 'Create';
			$item['title'] = $res;
			$item['author_xchan'] = $channel['channel_hash'];
			$item['owner_xchan'] = $poll[0]['author_xchan'];
			$item['allow_cid'] = '<' . $poll[0]['author_xchan'] . '>';
			$item['item_private'] = 1;
			$item['item_unseen'] = 0;
			$item['obj_type'] = 'Note';
			$item['author'] = channelx_by_n($channel['channel_id']);
			$item['obj'] = Activity::encode_item($item);

			// now reset the placeholders

			$item['verb'] = 'Create';
			$item['obj_type'] = 'Answer';
			unset($item['author']);

			$x = item_store($item);

			retain_item($poll[0]['id']);

			if($x['success']) {
				Master::Summon(['Notifier', 'like', $x['item_id']]);
				if (!empty($x['approval_id'])) {
					Master::Summon(['Notifier', 'like', $x['approval_id']]);
				}
			}
/*
			$r = q("select * from item where id = %d",
				intval($itemid)
			);
			if ($r) {
				xchan_query($r);
				$sync_item = fetch_post_tags($r);
				Libsync::build_sync_packet($channel['channel_id'], [ 'item' => [ encode_item($sync_item[0],true) ] ]);
			}
*/
		}

		$ret['success'] = true;
		$ret['message'] = t('Your vote has been submitted. Updates may not appear instantly.');
		json_return_and_die($ret);
	}
}









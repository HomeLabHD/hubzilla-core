<?php
namespace Zotlabs\Module;

use Zotlabs\Web\Controller;

class Request extends Controller
{

	private function mapVerb(string $verb) : string
	{
		$verbs = [
			'like'        => 'Like',
			'dislike'     => 'Dislike',
			'announce'    => 'Announce',
			'attendyes'   => 'Accept',
			'attendno'    => 'Reject',
			'attendmaybe' => 'TentativeAccept'
		];

		if (array_key_exists($verb, $verbs)) {
			return $verbs[$verb];
		}

		return EMPTY_STR;
	}

	private function like_response($arr) {

		$page_mode = (($arr['item']['item_thread_top'] && $_REQUEST['page_mode']) ? $_REQUEST['page_mode'] : 'r_preview');
		$conv_mode = (($_REQUEST['conv_mode']) ? $_REQUEST['conv_mode'] : 'network');

		if ($conv_mode === 'channel') {
			$parts = explode('@', $arr['owner_xchan']['xchan_addr']);
			profile_load($parts[0]);
		}

		$item_normal = item_normal();

		if ($page_mode === 'list') {
			$items = q("SELECT item.*, item.id AS item_id FROM item
				WHERE uid = %d $item_normal
				AND parent = %d",
				intval($arr['item']['uid']),
				intval($arr['item']['parent'])
			);
			xchan_query($items, true);
			$items = fetch_post_tags($items, true);
			$items = conv_sort($items, 'commented');
		}
		else {
			$activities = q("SELECT item.*, item.id AS item_id FROM item
				WHERE uid = %d $item_normal
				AND thr_parent = '%s'
				AND verb IN ('%s', '%s', '%s', '%s', '%s', '%s', 'Accept', 'Reject', 'TentativeAccept')",
				intval($arr['item']['uid']),
				dbesc($arr['item']['mid']),
				dbesc('Like'),
				dbesc('Dislike'),
				dbesc(ACTIVITY_SHARE),
				dbesc(ACTIVITY_ATTEND),
				dbesc(ACTIVITY_ATTENDNO),
				dbesc(ACTIVITY_ATTENDMAYBE)
			);
			xchan_query($activities, true);
			$items = array_merge([$arr['item']], $activities);
			$items = fetch_post_tags($items, true);
		}

		$ret = [
			'success' => 1,
			'orig_id' => $arr['orig_item_id'], //this is required for pubstream items where $item_id != $item['id']
			'id'      => $arr['item']['id'],
			'html'    => conversation($items, $conv_mode, true, $page_mode),
		];

		// mod photos
		if (isset($_REQUEST['reload']) && $_REQUEST['reload']) {
			$ret['reload'] = 1;
		}

		return $ret;

	}

	public function get() : string
	{

		if (!local_channel()) {
			killme();
		}

		$verb = self::mapVerb($_GET['verb']);

		if (!$verb) {
			killme();
		}

		$mid = strip_tags($_GET['mid']);
		$hash = get_observer_hash();
		$item_normal = item_normal();

		$r = q("SELECT xchan_hash, xchan_name as name, xchan_url as url, xchan_photo_s as photo FROM item
			LEFT JOIN xchan ON author_xchan = xchan_hash
			WHERE uid = %d
			AND parent = %d
			AND thr_parent = '%s'
			AND verb = '%s'
			AND item_thread_top = 0
			$item_normal",
			intval(local_channel()),
			dbesc($mid),
			dbesc($verb)
		);

		$ret = [
			'result' => $r,
			'action' => (($verb === 'Announce') ? 'jotShare' : 'dolike'),
			'action_label' => ((find_xchan_in_array($hash, $r)) ? t('- Remove yours') : t('+ Add yours'))
		];

		json_return_and_die($ret);

	}

}

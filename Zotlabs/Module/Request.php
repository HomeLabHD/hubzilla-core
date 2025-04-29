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


	private function processSubthreadRequest() : string
	{
		$mid = $_GET['mid'];
		$parent = intval($_GET['parent']);
		$module = strip_tags($_GET['module']);

		$items = items_by_thr_parent($mid, $parent);

		xchan_query($items,true,(($sys_item) ? local_channel() : 0));

		$items = fetch_post_tags($items,true);
	//	$items = conv_sort($items,'created');

		$ret['html'] = conversation($items, $module, true, 'r_preview');

		json_return_and_die($ret);
	}

	public function get() : string
	{

		if (!local_channel()) {
		//	killme();
		}

		if ($_GET['verb'] === 'comment') {
			return self::processSubthreadRequest();
		}

		$verb = self::mapVerb($_GET['verb']);

		if (!$verb) {
			killme();
		}


		$parent = intval($_GET['parent']);
		$mid = strip_tags($_GET['mid']);

		$observer_hash = get_observer_hash();
		$item_normal = item_normal();

		if (local_channel()) {
			$r = q("SELECT xchan_hash, xchan_name as name, xchan_url as url, xchan_photo_s as photo FROM item
				LEFT JOIN xchan ON author_xchan = xchan_hash
				WHERE uid = %d
				AND thr_parent = '%s'
				AND verb = '%s'
				AND item_thread_top = 0
				$item_normal
				ORDER BY item.created",
				intval(local_channel()),
				dbesc($mid),
				dbesc($verb)
			);
		}

		if (!$r) {

			$x = q("SELECT uid FROM item WHERE id = %d",
				intval($parent)
			);

			$sql_extra = item_permissions_sql($x[0]['uid'], $observer_hash);

			$r = q("SELECT xchan_hash, xchan_name as name, xchan_url as url, xchan_photo_s as photo FROM item
				LEFT JOIN xchan ON author_xchan = xchan_hash
				WHERE uid = %d
				AND thr_parent = '%s'
				AND verb = '%s'
				AND item_thread_top = 0
				$sql_extra
				$item_normal
				ORDER BY item.created",
				intval($x[0]['uid']),
				dbesc($mid),
				dbesc($verb)
			);
		}

		$ret = [
			'result' => $r
		];


		// TODO: check permission to like
		if ($observer_hash) {
			$ret['action'] = (($verb === 'Announce') ? 'jotShare' : 'dolike');
			$ret['action_label'] = ((find_xchan_in_array($observer_hash, $r)) ? t('- Remove yours') : t('+ Add yours'));
		}

		json_return_and_die($ret);

	}

}

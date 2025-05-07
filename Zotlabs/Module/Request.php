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
		xchan_query($items);

		$items = fetch_post_tags($items,true);

		$ret['html'] = conversation($items, $module, true, 'r_preview');

		json_return_and_die($ret);
	}

	public function get() : string
	{

		if ($_GET['verb'] === 'comment') {
			return self::processSubthreadRequest();
		}

		$verb = self::mapVerb($_GET['verb']);

		if (!$verb) {
			killme();
		}

		$text = get_response_button_text($_GET['verb']);
		$mid = strip_tags($_GET['mid']);
		$parent = intval($_GET['parent']);
		$observer_hash = get_observer_hash();


		$ret['result'] = item_activity_xchans($mid, $parent, $verb);

		// TODO: check permission to like
		if ($observer_hash) {
			$ret['action'] = (($verb === 'Announce') ? 'jotShare' : 'dolike');
			$ret['action_label'] = ((find_xchan_in_array($observer_hash, $ret['result'])) ? t('- Remove yours') : t('+ Add yours'));
		}

		$ret['title'] = $text['label'];

		json_return_and_die($ret);

	}

}

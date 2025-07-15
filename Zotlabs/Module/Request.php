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
			'accept'   => 'Accept',
			'reject'    => 'Reject',
			'tentativeaccept' => 'TentativeAccept'
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

		$offset = null;
		if ($_GET['verb'] === 'load') {
			$offset = intval($_GET['offset']);
		}

		$module = strip_tags($_GET['module']);

		$items = items_by_thr_parent($mid, $parent, $offset);
		xchan_query($items);

		$items = fetch_post_tags($items,true);

		if ($module === 'channel') {
			$parts = explode('@', $items[0]['owner']['xchan_addr']);
			profile_load($parts[0]);
		}

		$ret['html'] = conversation($items, $module, true, 'r_preview');

		json_return_and_die($ret);
	}

	public function get() : string
	{

		if (in_array($_GET['verb'], ['comment', 'load'])) {
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

		$commentable = $ret['result']['is_commentable'];
		unset($ret['result']['is_commentable']);

		if ($commentable) {
			$ret['action'] = (($verb === 'Announce') ? 'jotShare' : 'dolike');
			$ret['action_label'] = ((find_xchan_in_array($observer_hash, $ret['result'])) ? (($verb === 'Announce') ? t('+ Repeat again') : t('- Remove yours')) : t('+ Add yours'));
		}

		$ret['title'] = $text['label'];

		json_return_and_die($ret);

	}

}

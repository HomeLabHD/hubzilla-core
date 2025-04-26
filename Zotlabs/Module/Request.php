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

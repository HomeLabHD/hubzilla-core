<?php
namespace Zotlabs\Module;

use Zotlabs\Web\Controller;
use Zotlabs\Daemon\Master;
use Zotlabs\Lib\ActivityStreams;
use App;


class Emoji extends Controller {

	function init() {

		$shortname = argv(1);

		if (!$shortname) {
			killme();
		}

		$emojis = get_emojis();

		if (!isset($emojis[$shortname])) {
			killme();
		}

		$emoji = $emojis[$shortname];

hz_syslog(print_r($emoji, true));


		if (!file_exists($emoji['filepath'])) {
			killme();
		}

		$image = getimagesize($emoji['filepath']);

		if(ActivityStreams::is_as_request()) {
			$last_modified = date(ATOM_TIME, filemtime($emoji['filepath']));

			$obj = [
				'id' => z_root() . '/emoji/' . $shortname,
				'type' => 'Emoji',
				'name' => $emoji['shortname'],
				'updated' => $last_modified,
				'icon' => [
					'type' => 'Image',
					'mediaType' => $image['mime'],
					'url' => z_root() . '/' . $emoji['filepath']
				]
			];

			as_return_and_die($obj);
		}

		header('Content-Type: ' . $image['mime']);
		echo file_get_contents($emoji['filepath']);
		killme();
	}

}

<?php

namespace Zotlabs\Module;

use Zotlabs\Lib\PConfig;
use Zotlabs\Web\Controller;

class Feed extends Controller {

	function init() {
		if (argc() < 2) {
			killme();
		}

		if (observer_prohibited(true)) {
			killme();
		}

		$channel = channelx_by_nick(argv(1));
		if (!$channel) {
			killme();
		}

		$params['begin'] = $_REQUEST['date_begin'] ?? NULL_DATE;
		$params['end'] = $_REQUEST['date_end'] ?? '';
		$params['type'] = 'xml';
		$params['pages'] = ((!empty($_REQUEST['pages'])) ? intval($_REQUEST['pages']) : 0);
		$params['top'] = ((array_key_exists('top', $_REQUEST)) ? intval($_REQUEST['top']) : PConfig::Get($channel['channel_id'], 'system', 'channel_simple_feed', 1));
		$params['start'] = ((!empty($_REQUEST['start'])) ? intval($_REQUEST['start']) : 0);
		$params['records'] = ((!empty($_REQUEST['records'])) ? intval($_REQUEST['records']) : 10);
		$params['cat'] = ((!empty($_REQUEST['cat'])) ? escape_tags($_REQUEST['cat']) : '');
		$params['compat'] = ((!empty($_REQUEST['compat'])) ? intval($_REQUEST['compat']) : 0);
		$params['direction'] = ((!empty($_REQUEST['direction'])) ? dbesc($_REQUEST['direction']) : 'desc');

		if (!in_array($params['direction'], ['asc', 'desc'])) {
			$params['direction'] = 'desc';
		}

		logger('public feed request from ' . $_SERVER['REMOTE_ADDR'] . ' for ' . $channel['channel_address']);

		echo get_public_feed($channel, $params);

		killme();

	}

}

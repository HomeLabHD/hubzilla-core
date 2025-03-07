<?php

namespace Zotlabs\Module;

use App;
use Zotlabs\Lib\Config;
use Zotlabs\Lib\Libzot;
use Zotlabs\Web\Controller;
use Zotlabs\Web\HTTPSig;

require_once('include/items.php');
require_once('include/conversation.php');


class Home extends Controller {

	function init() {

		$ret = [];

		call_hooks('home_init', $ret);

		if (Libzot::is_zot_request()) {
			$key = Config::Get('system', 'prvkey');
			$ret = json_encode(Libzot::site_info());

			$headers                     = ['Content-Type' => 'application/x-zot+json', 'Digest' => HTTPSig::generate_digest_header($ret)];
			$headers['(request-target)'] = strtolower($_SERVER['REQUEST_METHOD']) . ' ' . $_SERVER['REQUEST_URI'];
			$h                           = HTTPSig::create_sig($headers, $key, z_root());
			HTTPSig::set_headers($h);

			echo $ret;
			killme();
		}

		$splash = ((argc() > 1 && argv(1) === 'splash') ? true : false);

		$channel = App::get_channel();
		if (local_channel() && $channel && $channel['xchan_url'] && !$splash) {
			$dest = $ret['startpage'] ?? '';
			if (!$dest)
				$dest = Config::Get('system', 'startpage');
			if (!$dest)
				$dest = z_root() . '/hq';

			goaway($dest);
		}

		if (remote_channel() && (!$splash) && $_SESSION['atoken']) {
			$r = q("select * from atoken where atoken_id = %d",
				intval($_SESSION['atoken'])
			);
			if ($r) {
				$x = channelx_by_n($r[0]['atoken_uid']);
				if ($x) {
					goaway(z_root() . '/channel/' . $x['channel_address']);
				}
			}
		}

		if (get_account_id() && !$splash) {
			goaway(z_root() . '/new_channel');
		}

	}

	function get($update = 0, $load = false) {

		$o = '';

		if (x($_SESSION, 'theme'))
			unset($_SESSION['theme']);
		if (x($_SESSION, 'mobile_theme'))
			unset($_SESSION['mobile_theme']);

		$splash = ((argc() > 1 && argv(1) === 'splash') ? true : false);

		call_hooks('home_content', $o);
		if ($o)
			return $o;

		$frontpage = Config::Get('system', 'frontpage');
		if ($frontpage) {
			if (strpos($frontpage, 'include:') !== false) {
				$file = trim(str_replace('include:', '', $frontpage));
				if (file_exists($file)) {
					App::$page['template'] = 'full';
					App::$page['title']    = t('$Projectname');
					$o                     .= file_get_contents($file);
					return $o;
				}
			}
			if (strpos($frontpage, 'http') !== 0)
				$frontpage = z_root() . '/' . $frontpage;
			if (intval(Config::Get('system', 'mirror_frontpage'))) {
				$o = '<html><head><title>' . t('$Projectname') . '</title></head><body style="margin: 0; padding: 0; border: none;" ><iframe src="' . $frontpage . '" width="100%" height="100%" style="margin: 0; padding: 0; border: none;" ></iframe></body></html>';
				echo $o;
				killme();
			}
			goaway($frontpage);
		}

		$o .= '<div class="generic-content-wrapper">';

		$sitename = Config::Get('system', 'sitename');
		if ($sitename) {
			$o .= '<div class="section-title-wrapper">';
			$o .= '<h2 class="">' . sprintf(t('Welcome to %s'), $sitename) . '</h2>';
			$o .= '</div>';

		}

		$o .= '<div class="section-content-wrapper">';

		$loginbox = Config::Get('system', 'login_on_homepage');
		if (intval($loginbox) || $loginbox === false)
			$o .= login(true);

		$o .= '</div>';
		$o .= '</div>';

		return $o;

	}

}

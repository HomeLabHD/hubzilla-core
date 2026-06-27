<?php /** @file */

namespace Zotlabs\Web;

use App;
use Zotlabs\Lib\Text;
use GuzzleHttp\Psr7\Request;


class WebServer {

	public function run(): void {


		/*
		 * Bootstrap the application, load configuration, load modules, load theme, etc.
		 */

		require_once('boot.php');

		$installed = sys_boot();

		$this->createRequest();

		App::$language = get_best_language();
		load_translation_table(App::$language, !$installed);


		/**
		 *
		 * Important stuff we always need to do.
		 *
		 * The order of these may be important so use caution if you think they're all
		 * intertwingled with no logical order and decide to sort it out. Some of the
		 * dependencies have changed, but at least at one time in the recent past - the
		 * order was critical to everything working properly
		 *
		 */

		if(App::$session) {
			App::$session->start();
	  	}
  		else {
			session_start();
			register_shutdown_function('session_write_close');
  		}

		/**
		 * Language was set earlier, but we can over-ride it in the session.
		 * We have to do it here because the session was just now opened.
		 */

		if(array_key_exists('system_language',$_REQUEST)) {
			if(strlen($_REQUEST['system_language']))
				$_SESSION['language'] = $_REQUEST['system_language'];
			else
				unset($_SESSION['language']);
		}

		if ((!empty($_SESSION['language'])) && ($_SESSION['language'] !== App::$language)) {
			App::$language = $_SESSION['language'];
			load_translation_table(\App::$language);
		}

		if (!empty($_GET['zid']) && $installed) {
			App::$query_string = strip_zids(App::$query_string);
			if(! local_channel()) {
				if (!isset($_SESSION['my_address'])) {
					$_SESSION['my_address'] = Text::escape_tags($_GET['zid']);
					$_SESSION['authenticated'] = 0;
				}
				if(!$_SESSION['authenticated']) {
					zid_init();
				}
			}
		}

		if (!empty($_GET['zat']) && $installed) {
			App::$query_string = strip_zats(App::$query_string);
			if(! local_channel()) {
				zat_init();
			}
		}

		if (!empty($_REQUEST['owt']) && $installed) {
			$token = $_REQUEST['owt'];
			App::$query_string = strip_query_param(App::$query_string,'owt');
			owt_init($token);
		}

		if(!empty($_SESSION['authenticated']) || !empty($_POST['auth-params']) || App::$module === 'login') {
			require('include/auth.php');
		}

		if (!$installed) {
			/* Allow an exception for the view module so that pcss will be interpreted during installation */
			if(App::$module != 'view') {
				App::$module = 'setup';
			}
		}
		else {

			/*
			 * check_config() is responsible for running update scripts. These automatically
			 * update the DB schema whenever we push a new one out. It also checks to see if
			 * any plugins have been added or removed and reacts accordingly.
			 */

			check_config();
		}

		$this->create_channel_links();

		$Router = new Router();

		$this->initialise_content();

		$Router->Dispatch();

		call_hooks('page_end', App::$page['content']);

		construct_page();
	}


	public function createRequest()
	{
		$input = null;

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$input = file_get_contents('php://input');
		}

		$headers = [];

		if (isset($_SERVER['CONTENT_TYPE'])) {
			$headers['content-type'] = $_SERVER['CONTENT_TYPE'];
		}

		if (isset($_SERVER['CONTENT_LENGTH'])) {
			$headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
		}

		foreach ($_SERVER as $k => $v) {
			if (str_starts_with($k, 'HTTP_')) {
				$field = str_replace('_', '-', strtolower(substr($k, 5)));
				$headers[$field] = $v;
			}
		}

		App::$request = new Request(
			$_SERVER['REQUEST_METHOD'],
			z_root() . ((App::$originalRequest) ?? $_SERVER['REQUEST_URI']),
			$headers,
			$input
		);

	}

	private function initialise_content() {

		/* initialise content region */

		if(empty(App::$page['content'])) {
			App::$page['content'] = '';
		}

		call_hooks('page_content_top', App::$page['content']);

	}

	private function create_channel_links() {

		/* Initialise the Link: response header if this is a channel page.
		 * This cannot be done inside the channel module because some protocol
		 * addons over-ride the module functions and these links are common
		 * to all protocol drivers; thus doing it here avoids duplication.
		 */

		if (App::$module === 'channel' && argc() > 1) {
			App::$channel_links = [
				[
					'rel'  => 'lrdd',
					'type' => 'application/xrd+xml',
					'url'  => z_root() . '/xrd?f=&uri=acct%3A' . argv(1) . '%40' . App::get_hostname()
				],
				[
					'rel'  => 'jrd',
					'type' => 'application/jrd+json',
					'url'  => z_root() . '/.well-known/webfinger?f=&resource=acct%3A' . argv(1) . '%40' . App::get_hostname()
				],
			];
			$x = [ 'channel_address' => argv(1), 'channel_links' => App::$channel_links ];
			call_hooks('channel_links', $x );
			App::$channel_links = $x['channel_links'];
			header('Link: ' . \App::get_channel_links());
		}
	}

}

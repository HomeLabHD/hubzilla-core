<?php
namespace Zotlabs\Module;

use App;
use Zotlabs\Web\Controller;
use Zotlabs\Web\HTTPSig;
use Zotlabs\Lib\Libzot;
use Zotlabs\Lib\SConfig;

class Magic extends Controller {

	function init() {

		logger('mod_magic: invoked', LOGGER_DEBUG);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = $_POST;
		} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$data = $_GET;
		} else {
			http_status_exit(405, 'Method Not Allowed');
		}

		logger('request method: ' . print_r($_SERVER['REQUEST_METHOD'], true), LOGGER_DATA);
		logger('args: ' . print_r($data, true), LOGGER_DATA);

		$bdest    = $data['bdest'] ?? '';
		$owa      = $data['owa'] ?? 0;
		$delegate = $data['delegate'] ?? '';

		// bdest is preferred as it is hex-encoded and can survive url rewrite and argument parsing

		if (!$bdest) {
			http_status_exit(400, 'Bad Request');
		}

		$dest = hex2bin($bdest);
		$parsed = parse_url($dest);

		if (!$parsed) {
			http_status_exit(400, 'Bad Request');
		}

		$basepath = unparse_url(array_filter(
			$parsed,
			fn (string $key) => in_array($key, ['scheme', 'host', 'port']),
			ARRAY_FILTER_USE_KEY
		));

		$owapath = SConfig::get($basepath, 'system', 'openwebauth', $basepath . '/owa');

		// This is ready-made for a plugin that provides a blacklist or "ask me" before blindly authenticating.
		// By default, we'll proceed without asking.

		$arr = [
			'channel_id'  => local_channel(),
			'destination' => $dest,
			'proceed'     => true
		];

		call_hooks('magic_auth',$arr);

		$dest = $arr['destination'];

		if (!$arr['proceed']) {
			goaway($dest);
		}

		if (get_observer_hash() && str_starts_with($dest, z_root())) {

			// We are already authenticated on this site and a registered observer.
			// First check if this is a delegate request on the local system and process accordingly.
			// Otherwise redirect.

			if ($delegate) {

				$r = q("select * from channel left join hubloc on channel_hash = hubloc_hash where hubloc_addr = '%s' limit 1",
					dbesc($delegate)
				);

				if ($r) {
					$c = array_shift($r);
					if (perm_is_allowed($c['channel_id'],get_observer_hash(),'delegate')) {
						$tmp = $_SESSION;
						$_SESSION['delegate_push']    = $tmp;
						$_SESSION['delegate_channel'] = $c['channel_id'];
						$_SESSION['delegate']         = get_observer_hash();
						$_SESSION['account_id']       = intval($c['channel_account_id']);

						change_channel($c['channel_id']);
					}
				}
			}

			goaway($dest);
		}

		if (local_channel()) {
			$channel = App::get_channel();

			// OpenWebAuth

			if ($owa) {

				$dest = strip_zids($dest);
				$dest = strip_query_param($dest,'f');

				// We now post to the OWA endpoint. This improves security by providing a signed digest

				$data = json_encode([ 'OpenWebAuth' => random_string() ]);

				$headers = [];
				$headers['Accept'] = 'application/x-zot+json' ;
				$headers['Content-Type'] = 'application/x-zot+json' ;
				$headers['X-Open-Web-Auth'] = random_string();
				$headers['Host'] = $parsed['host'];
				$headers['(request-target)'] = 'get /owa';

				$headers = HTTPSig::create_sig($headers,$channel['channel_prvkey'], channel_url($channel),true,'sha512');
				$redirects = 0;

				$x = z_fetch_url($owapath, false, $redirects, ['headers' => $headers]);

				logger('owa fetch returned: ' . print_r($x,true),LOGGER_DATA);

				if ($x['success']) {
					$j = json_decode($x['body'],true);
					if ($j['success'] && $j['encrypted_token']) {
						// decrypt the token using our private key
						$token = '';
						openssl_private_decrypt(base64url_decode($j['encrypted_token']), $token, $channel['channel_prvkey']);
						$x = strpbrk($dest,'?&');
						// redirect using the encrypted token which will be exchanged for an authenticated session
						$args = (($x) ? '&owt=' . $token : '?owt=' . $token) . (($delegate) ? '&delegate=1' : '');
						goaway($dest . $args);
					}
					else {
						$o = '<h1>OWA ERROR</h1>';
						if (!empty($j['message'])) {
							$o .= '<h2>' . $j['message'] . '</h2>';
						}
						$o .= '<a href=' . $dest . '>' . $dest . '</a>';

						echo $o;
					}
				}
			}
		}

		killme();

	}

}

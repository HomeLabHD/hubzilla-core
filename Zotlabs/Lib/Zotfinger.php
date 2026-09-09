<?php

namespace Zotlabs\Lib;

use Zotlabs\Lib\Config;
use Zotlabs\Web\HTTPSig;
use Zotlabs\Web\HTTPHeaders;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Message;
use HttpSignature\HttpMessageSigner;

class Zotfinger {

	static function exec($resource, $channel = null, $verify = true, $recurse = true) {

		if(! $resource) {
			return false;
		}

		$m = parse_url($resource);

		$data = json_encode([ 'zot_token' => random_string() ]);

		if($channel && $m) {
			if (Config::Get('system', 'send_rfc9421')) {
				$signer = new HttpMessageSigner();
				$request = new Request(
					'POST',
					$resource,
					[
						'Accept' => 'application/x-zot+json',
						'Content-Type' => 'application/x-zot+json',
						'X-API-Token' => random_string(),
						'Content-Digest' => $signer->createContentDigestHeader($data),
						'Host' => $m['host'],
						'Date' => gmdate('D, d M Y H:i:s T'),
					],
					$data
				);

				$signer->setPrivateKey($channel['channel_prvkey'])
					->setAlgorithm('rsa-v1_5-sha256')
					->setKeyId(channel_url($channel))
					->setCreated(time())
					->setExpires(time() + 3600);

				$coveredFields = '("@method" "@target-uri" "host" "date" "x-api-token" "content-digest" "content-type" "accept")';
				$request = $signer->signRequest($coveredFields, $request);
				$signedHeaders = $signer->getHeaders($request);
				$curlHeaders = [];
				foreach ($signedHeaders as $key => $value) {
					$curlHeaders[] = $key . ': ' . $value;
				}
			}
            else {
				$headers = [
					'Accept'           => 'application/x-zot+json',
					'Content-Type'     => 'application/x-zot+json',
					'X-Zot-Token'      => random_string(),
					'Digest'           => HTTPSig::generate_digest_header($data),
					'Host'             => $m['host'],
					'(request-target)' => 'post ' . get_request_string($resource)
				];
				$curlHeaders = HTTPSig::create_sig($headers,$channel['channel_prvkey'],channel_url($channel),false);
			}
		}
		else {
			$curlHeaders = [ 'Accept: application/x-zot+json' ];
		}

		$result = [];
		$redirects = 0;

		$start_timestamp = microtime(true);
		$x = z_post_url($resource, $data, $redirects, ['headers' => $curlHeaders]);

		logger('logger_stats_data cmd:Zotfinger' . ' start:' . $start_timestamp . ' ' . 'end:' . microtime(true) . ' meta:' . $resource . '#' . random_string(16));
		btlogger('Zotfinger');

		logger('fetch: ' . print_r($x,true), LOGGER_DATA);

        if (in_array(intval($x['return_code']), [ 404, 410 ]) && $recurse) {

            // The resource has been deleted or doesn't exist at this location.
            // Try to find another nomadic resource for this channel and return that.

            // First, see if there's a hubloc for this site. Fetch that record to
            // obtain the nomadic identity hash. Then use that to find any additional
            // nomadic locations.

            $h = Activity::get_actor_hublocs($resource, 'zot6');
            if ($h) {
                // mark this location deleted
                hubloc_delete($h[0]);
                $hubs = Activity::get_actor_hublocs($h[0]['hubloc_hash']);
                if ($hubs) {
                    foreach ($hubs as $hub) {
                        if ($hub['hubloc_id_url'] !== $resource && !$hub['hubloc_deleted']) {
                            return self::exec($hub['hubloc_id_url'], $channel, $verify);
                        }
                    }
                }
            }
        }

		if($x['success']) {
			$response = Message::parseResponse($x['header'] . $x['body']);

			if ($verify) {
				$result['signature'] = HTTPSig::verify($x, EMPTY_STR, 'zot6', $response, $request ?? null);
			}

			$result['data'] = json_decode($x['body'],true);

			if($result['data'] && is_array($result['data']) && array_key_exists('encrypted',$result['data']) && $result['data']['encrypted']) {
				$result['data'] = json_decode(Crypto::unencapsulate($result['data'],Config::Get('system','prvkey')),true);
			}

			logger('decrypted: ' . print_r($result,true), LOGGER_DATA);

			return $result;
		}

		return false;
	}



}

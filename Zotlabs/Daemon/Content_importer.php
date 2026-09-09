<?php

namespace Zotlabs\Daemon;

use Zotlabs\Web\HTTPSig;
use Zotlabs\Lib\Config;
use Zotlabs\Lib\PConfig;
use GuzzleHttp\Psr7\Request;
use HttpSignature\HttpMessageSigner;

require_once('include/cli_startup.php');
require_once('include/attach.php');
require_once('include/import.php');

class Content_importer {

	static public function run($argc,$argv) {
		cli_startup();

		$page = $argv[1];
		$since = $argv[2];
		$until = $argv[3];
		$channel_address = $argv[4];
		$hz_server = urldecode($argv[5]);

		$m = parse_url($hz_server);

		$channel = channelx_by_nick($channel_address);
		if(! $channel) {
			logger('channel not found');
			return;
		}

		if (Config::Get('system', 'send_rfc9421')) {
			$signer = new HttpMessageSigner();
			$request = new Request(
				'GET',
				$hz_server . '/api/z/1.0/item/export_page?since=' . urlencode($since) . '&until=' . urlencode($until) . '&page=' . $page,
				[
					'X-API-Token' => random_string(),
					'X-API-Request'    => $hz_server . '/api/z/1.0/item/export_page?since=' . urlencode($since) . '&until=' . urlencode($until) . '&page=' . $page ,
					'Host' => $m['host'],
					'Date' => gmdate('D, d M Y H:i:s T'),
				]
			);

			$signer->setPrivateKey($channel['channel_prvkey'])
				->setAlgorithm('rsa-v1_5-sha256')
				->setKeyId(channel_url($channel))
				->setCreated(time())
				->setExpires(time() + 3600);

			$coveredFields = '("@method" "@target-uri" "host" "date" "x-api-token" "x-api-request")';
			$request = $signer->signRequest($coveredFields, $request);
			$signedHeaders = $signer->getHeaders($request);
			$curlHeaders = [];
			foreach ($signedHeaders as $key => $value) {
				$curlHeaders[] = $key . ': ' . $value;
			}
		}
		else {
			$headers = [
				'X-API-Token'      => random_string(),
				'X-API-Request'    => $hz_server . '/api/z/1.0/item/export_page?since=' . urlencode($since) . '&until=' . urlencode($until) . '&page=' . $page ,
				'Host'             => $m['host'],
				'(request-target)' => 'get /api/z/1.0/item/export_page?since=' . urlencode($since) . '&until=' . urlencode($until) . '&page=' . $page ,
			];

			$curlHeaders = HTTPSig::create_sig($headers,$channel['channel_prvkey'], channel_url($channel),true,'sha512');
		}


		$redirects = 0;
		$x = z_fetch_url(
			$hz_server . '/api/z/1.0/item/export_page?since=' . urlencode($since) . '&until=' . urlencode($until) . '&page=' . $page,
			false,
			$redirects,
			[ 'headers' => $curlHeaders ]
		);

		// logger('item fetch: ' . print_r($x,true));

		if(! $x['success']) {
			logger('no API response',LOGGER_DEBUG);
			killme();
		}

		$j = json_decode($x['body'], true);

		if($j && empty($j['item'])) {
			PConfig::Set($channel['channel_id'], 'import', 'content_completed', 1);
			return;
		}

		$saved_notification_flags = notifications_off($channel['channel_id']);

		import_items($channel,$j['item'],false,((array_key_exists('relocate',$j)) ? $j['relocate'] : null));

		notifications_on($channel['channel_id'], $saved_notification_flags);

		PConfig::Set($channel['channel_id'], 'import', 'content_progress', [
			'items_total' => $j['items_total'],
			'items_page' => $j['items_page'],
			'items_current_page' => count($j['item']),
			'last_page' => $page,
			'next_cmd' => ['Content_importer', sprintf('%d',$page + 1), $since, $until, $channel['channel_address'], urlencode($hz_server)]
		]);

		$page++;

		Master::Summon([ 'Content_importer', sprintf('%d',$page), $since, $until, $channel['channel_address'], urlencode($hz_server) ]);

		return;
	}
}

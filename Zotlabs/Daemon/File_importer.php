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

class File_importer {

	static public function run($argc,$argv) {

		cli_startup();

		$page = $argv[1];
		$channel_address = $argv[2];
		$hz_server = urldecode($argv[3]);

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
				$hz_server . '/api/z/1.0/file/export_page?records=1&page=' . $page,
				[
					'X-API-Token' => random_string(),
					'X-API-Request' => $hz_server . '/api/z/1.0/file/export_page?records=1&page=' . $page,
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
				'X-API-Request'    => $hz_server . '/api/z/1.0/file/export_page?records=1&page=' . $page,
				'Host'             => $m['host'],
				'(request-target)' => 'get /api/z/1.0/file/export_page?records=1&page=' . $page,
			];

			$curlHeaders = HTTPSig::create_sig($headers,$channel['channel_prvkey'],channel_url($channel),true,'sha512');
		}


		// TODO: implement total count
		$redirects = 0;
		$x = z_fetch_url(
			$hz_server . '/api/z/1.0/file/export_page?records=1&page=' . $page,
			false,
			$redirects,
			[ 'headers' => $curlHeaders ]
		);
		// logger('file fetch: ' . print_r($x,true));

		if(! $x['success']) {
			logger('no API response',LOGGER_DEBUG);
			killme();
		}

		$j = json_decode($x['body'],true);

		if(! is_array($j['results'][0]['attach']) || ! count($j['results'][0]['attach'])) {
			PConfig::Set($channel['channel_id'], 'import', 'files_completed', 1);
			return;
		}

		$r = sync_files($channel, $j['results']);

		PConfig::Set($channel['channel_id'], 'import', 'files_progress', [
			'files_total' => $j['total'],
			'files_page' => 1, // export page atm returns just one file
			'last_page' => $page,
			'next_cmd' => ['File_importer',sprintf('%d',$page + 1), $channel['channel_address'], urlencode($hz_server)]
		]);

		$page++;

		Master::Summon([ 'File_importer',sprintf('%d',$page), $channel['channel_address'], urlencode($hz_server) ]);

		return;
	}
}

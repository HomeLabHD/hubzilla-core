<?php

namespace Zotlabs\Lib;

require_once('library/jsonld/jsonld.php');

class LDSignatures {


	static function verify($data,$pubkey) {
		$expand_and_check_unsafe = true;

		$ohash = self::hash(self::signable_options($data['signature']), $expand_and_check_unsafe);
		$dhash = self::hash(self::signable_data($data), $expand_and_check_unsafe);

		$x = Crypto::verify($ohash . $dhash,base64_decode($data['signature']['signatureValue']), $pubkey);
		logger('LD-verify: ' . intval($x));

		return $x;
	}

	static function dopplesign(&$data,$channel) {
	// remove for the time being - performance issues
	//	$data['magicEnv'] = self::salmon_sign($data,$channel);
		return self::sign($data,$channel);
	}

	static function sign($data,$channel) {

		$options = [
			'type' => 'RsaSignature2017',
			'nonce' => random_string(64),
			'creator' => z_root() . '/channel/' . $channel['channel_address'],
			'created' => datetime_convert('UTC','UTC', 'now', 'Y-m-d\TH:i:s\Z')
		];

		$ohash = self::hash(self::signable_options($options));
		$dhash = self::hash(self::signable_data($data));
		$options['signatureValue'] = base64_encode(Crypto::sign($ohash . $dhash,$channel['channel_prvkey']));

		$signed = array_merge([
			'@context' => [
				ACTIVITYSTREAMS_JSONLD_REV,
				'https://w3id.org/security/v1' ],
			],$options);

		return $signed;
	}


	static function signable_data($data) {

		$newdata = [];
		if($data) {
			foreach($data as $k => $v) {
				if(! in_array($k,[ 'signature' ])) {
					$newdata[$k] = $v;
				}
			}
		}
		return json_encode($newdata,JSON_UNESCAPED_SLASHES);
	}


	static function signable_options($options) {

		$newopts = [ '@context' => 'https://w3id.org/identity/v1' ];
		if($options) {
			foreach($options as $k => $v) {
				if(! in_array($k,[ 'type','id','signatureValue' ])) {
					$newopts[$k] = $v;
				}
			}
		}
		return json_encode($newopts,JSON_UNESCAPED_SLASHES);
	}

	static function hash($obj, $expand_and_check_unsafe = false) {
		return hash('sha256', self::normalise($obj, $expand_and_check_unsafe));
	}

	static function normalise($data, $expand_and_check_unsafe) {
		$ret = '';

		if(is_string($data)) {
			$data = json_decode($data);
		}

		if(! is_object($data))
			return $ret;

		jsonld_set_document_loader('jsonld_document_loader');

		if ($expand_and_check_unsafe) {
			$expanded = jsonld_expand($data);

			if (self::contains_unsafe_keys($expanded)) {
				logger('contains_unsafe_keys: ' . print_r($data,true));
				throw new \Exception('json-ld graph modification operation detected');
			}
		}

		try {
			$ret = jsonld_normalize($data,[ 'algorithm' => 'URDNA2015', 'format' => 'application/nquads' ]);
		}
		catch (\Exception $e) {
			// Don't log the exception - this can exhaust memory
			// logger('normalise error:' . print_r($e,true));
			logger('normalise error: ' . print_r($data,true));
		}

		return $ret;
	}

	static function salmon_sign($data,$channel) {

		$arr       = $data;
		$data      = json_encode($data,JSON_UNESCAPED_SLASHES);
		$data      = base64url_encode($data, false); // do not strip padding
		$data_type = 'application/activity+json';
		$encoding  = 'base64url';
		$algorithm = 'RSA-SHA256';
		$keyhash   = base64url_encode(z_root() . '/channel/' . $channel['channel_address']);

		$data = str_replace(array(" ","\t","\r","\n"),array("","","",""),$data);

		// precomputed base64url encoding of data_type, encoding, algorithm concatenated with periods

		$precomputed = '.' . base64url_encode($data_type,false) . '.YmFzZTY0dXJs.UlNBLVNIQTI1Ng==';

		$signature  = base64url_encode(Crypto::sign($data . $precomputed,$channel['channel_prvkey']));

		return ([
			'id'          => $arr['id'],
			'meData'      => $data,
			'meDataType'  => $data_type,
			'meEncoding'  => $encoding,
			'meAlgorithm' => $algorithm,
			'meCreator'   => z_root() . '/channel/' . $channel['channel_address'],
			'meSignatureValue' => $signature
		]);

	}

	static function contains_unsafe_keys(array|object $data, int $depth = 0): bool
	{
		if ($depth > 64) {
			return true;
		}

		$unsafe_keys = ['@graph', '@included', '@reverse'];

		if (is_object($data)) {
			$data = (array) $data;
		}

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				//
				// We can't use `in_array` since the keys may contain more than
				// just the keyword after expansion, typically "_:@included"
				// for an unnamed node with the "@included" key.
				//
				// So we use `array_filter` with a callback instead:
				$matches = array_filter($unsafe_keys, fn ($k) => strpos($key, $k) !== false);

				if (!empty($matches)) {
					return true;
				}

				if (is_array($value) || is_object($value)) {
					if (self::contains_unsafe_keys($value, $depth + 1)) {
						return true;
					}
				}
			}
		}

		return false;
	}

}

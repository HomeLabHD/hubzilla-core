<?php

namespace Zotlabs\Lib;

use Root23\JsonCanonicalizer\JsonCanonicalizer;
use StephenHill\Base58;

class JcsEddsa2022 {

	/**
	 * Sign arbitrary data with the keys of the provided channel.
	 *
	 * @param $data				The data to be signed.
	 * @param array $channel	A channel as an array of key/value pairs.
	 *
	 * @return An array with the following fields:
	 *		- `type`: The type of signature, always `DataIntegrityProof`.
	 *		- `cryptosuite`: The cryptographic algorithm used, always `eddsa-jcs-2022`.
	 *		- `created`: The UTC date and timestamp when the signature was created.
	 *		- `verificationMethod`: The channel URL and the public key separated by a `#`.
	 *		- `proofPurpose`: The purpose of the signature, always `assertionMethod`.
	 *		- `proofValue`: The signature itself.
	 *
	 * @throws JcsEddsa2022SignatureException if the channel is missing, or
	 * don't have valid keys.
	 */
	public function sign($data, $channel): array {
		if (!is_array($channel) || !isset($channel['channel_epubkey'], $channel['channel_eprvkey'])) {
			throw new JcsEddsa2022SignException('Invalid or missing channel provided.');
		}

		$base58 = new Base58();
		$pubkey = (new Multibase())->publicKey($channel['channel_epubkey']);
		$options = [
			'type' => 'DataIntegrityProof',
			'cryptosuite' => 'eddsa-jcs-2022',
			'created' => datetime_convert('UTC', 'UTC', 'now', ATOM_TIME),
			'verificationMethod' => channel_url($channel) . '#' . $pubkey,
			'proofPurpose' => 'assertionMethod',
		];

		$optionsHash = $this->hash($this->signableOptions($options), true);
		$dataHash = $this->hash($this->signableData($data), true);

		$options['proofValue'] = 'z' . $base58->encode(sodium_crypto_sign_detached($optionsHash . $dataHash,
				sodium_base642bin($channel['channel_eprvkey'], SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING)));

		return $options;
	}

	public function verify($data, $publicKey) {
		$base58 = new Base58();
		$encodedSignature = $data['proof']['proofValue'] ?? '';
		if (!str_starts_with($encodedSignature,'z')) {
			return false;
		}

		$encodedSignature = substr($encodedSignature, 1);
		$optionsHash = $this->hash($this->signableOptions($data['proof']), true);
		$dataHash = $this->hash($this->signableData($data),true);

		try {
			$result = sodium_crypto_sign_verify_detached($base58->decode($encodedSignature), $optionsHash . $dataHash,
				(new Multibase())->decode($publicKey, true));

			logger('SignatureVerify (eddsa-jcs-2022) ' . (($result) ? 'true' : 'false'));

			return $result;
		}
		catch (\Exception $e) {
			logger('verify exception:' .  $e->getMessage());
		}

		return false;
	}

	public function signableData($data) {
		$signableData = [];
		if ($data) {
			foreach ($data as $k => $v) {
				if (!in_array($k, ['proof', 'signature'])) {
					$signableData[$k] = $v;
				}
			}
		}
		return $signableData;
	}

	public function signableOptions($options) {
		$signableOptions = [];

		if ($options) {
			foreach ($options as $k => $v) {
				if ($k !== 'proofValue') {
					$signableOptions[$k] = $v;
				}
			}
		}
		return $signableOptions;
	}

	public function hash($obj, $binary = false) {
		return hash('sha256', $this->canonicalize($obj), $binary);
	}

	public function canonicalize($data) {
		$canonicalizer = new JsonCanonicalizer();
		return $canonicalizer->canonicalize($data);
	}

}

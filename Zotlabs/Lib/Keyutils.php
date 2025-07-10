<?php

namespace Zotlabs\Lib;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;

/**
 * Keyutils
 * Convert RSA keys between various formats
 */
class Keyutils {

	/**
	 * @param string $m modulo
	 * @param string $e exponent
	 * @return string
	 */
	public static function meToPem(string $m, string $e): string
	{
		$parsedKey = PublicKeyLoader::load([
			'e' => new BigInteger($e, 256),
			'n' => new BigInteger($m, 256)
		]);
		if (method_exists($parsedKey, 'getPublicKey')) {
			$parsedKey = $parsedKey->getPublicKey();
		}
		return $parsedKey->toString('PKCS8');
	}

	/**
	 * @param string key
	 * @return string
	 */
    public static function rsaToPem(string $key): string
	{
		$parsedKey = PublicKeyLoader::load($key);
		if (method_exists($parsedKey, 'getPublicKey')) {
			$parsedKey = $parsedKey->getPublicKey();
		}
		return $parsedKey->toString('PKCS8');
	}

	/**
	 * @param string key
	 * @return string
	 */
	public static function pemToRsa(string $key): string
	{
		$parsedKey = PublicKeyLoader::load($key);
		if (method_exists($parsedKey, 'getPublicKey')) {
			$parsedKey = $parsedKey->getPublicKey();
		}
		return $parsedKey->toString('PKCS1');
	}

	/**
	 * @param string $key key
	 * @param string $m reference modulo
	 * @param string $e reference exponent
	 */
	public static function pemToMe(string $key): array
	{
		$parsedKey = PublicKeyLoader::load($key);
		if (method_exists($parsedKey, 'getPublicKey')) {
			$parsedKey = $parsedKey->getPublicKey();
		}
		$raw = $parsedKey->toString('Raw');

		$m = $raw['n'];
		$e = $raw['e'];

		return [$m->toBytes(), $e->toBytes()];
	}

	/**
	 * @param string $pubkey
	 * @return string
	 */
	public static function salmonKey(string $pubkey): string
	{
		[$m, $e] = self::pemToMe($pubkey);
		/** @noinspection PhpRedundantOptionalArgumentInspection */
		return 'RSA' . '.' . base64url_encode($m, true) . '.' . base64url_encode($e, true);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public static function convertSalmonKey(string $key): string
	{
		if (str_contains($key, ',')) {
			$rawkey = substr($key, strpos($key, ',') + 1);
		} else {
			$rawkey = substr($key, 5);
		}

		$key_info = explode('.', $rawkey);

		$m = base64url_decode($key_info[1]);
		$e = base64url_decode($key_info[2]);

		return self::meToPem($m, $e);
	}

}

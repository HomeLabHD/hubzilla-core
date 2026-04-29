<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Mario Vavti <mario@mariovavti.com>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

class Url {

	/**
	 * @brief Adds a zid parameter to a url.
	 *
	 * @param string $s
	 *   The url to accept the zid
	 * @param string $address
	 *   $address to use instead of session environment
	 * @return string
	 */
	public static function zid(string $url, string $address = ''): string
	{
		if (!$url || strpos($url, 'zid=') !== false) {
			return $url;
		}

		$parts = parse_url($url);
		if ($parts === false) {
			return $url;
		}

		$mine   = get_my_url();
		$myaddr = $address ?: get_my_address();

		if (!$mine || !$myaddr) {
			return $url;
		}

		$mine_parts = parse_url($mine);
		$same_host = isset($mine_parts['host'], $parts['host']) && strcasecmp($mine_parts['host'], $parts['host']) === 0;

		if ($same_host) {
			return $url;
		}

		$query = [];
		if (!empty($parts['query'])) {
			parse_str($parts['query'], $query);
		}

		$query['zid'] = $myaddr;
		$parts['query'] = http_build_query($query);

		$hookdata = [
			'url' => $url,
			'zid' => urlencode($myaddr),
			'result' => self::unparse($parts)
		];

		/**
		 * @hooks zid
		 *   Called when adding the observer's zid to a URL.
		 *   * \e string \b url - url to accept zid
		 *   * \e string \b zid - urlencoded zid
		 *   * \e string \b result - the return string we calculated, change it if you want to return something else
		 */
		call_hooks('zid', $hookdata);

		return $hookdata['result'];
	}


	/**
	 * Reconstructs a URL from its parsed components.
	 *
	 * This function takes a parsed URL as an associative array and reconstructs
	 * the URL based on the specified components (scheme, host, port, user, pass, path, query, fragment).
	 * You can specify which components should be included in the final URL by passing the optional
	 * `$parts` array. The function will return the complete URL string formed by combining
	 * only the parts that exist in both the parsed URL and the `$parts` array.
	 *
	 * @param array $parsed_url The parsed URL components as an associative array.
	 *                          The array can include keys like 'scheme', 'host', 'port', 'user', 'pass',
	 *                          'path', 'query', 'fragment'.
	 *
	 * @param array $parts An optional array that specifies which components of the URL
	 *                     should be included in the final string. Defaults to:
	 *                     ['scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'].
	 *                     If any of the components are not required, they can be omitted from the array.
	 *
	 * @return string The reconstructed URL as a string.
	 */
	public static function unparse(array $parsed_url, array $parts = ['scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment']): string {
		$url_parts = [];

		if (in_array('scheme', $parts) && array_key_exists('scheme', $parsed_url)) {
			$url_parts[] = $parsed_url['scheme'] . '://';
		}

		if (in_array('user', $parts) && array_key_exists('user', $parsed_url)) {
			$url_parts[] = $parsed_url['user'];
			if (in_array('pass', $parts) && array_key_exists('pass', $parsed_url)) {
				$url_parts[] = ':' . $parsed_url['pass'];
			}
			$url_parts[] = '@';
		}

		if (in_array('host', $parts) && array_key_exists('host', $parsed_url)) {
			$url_parts[] = $parsed_url['host'];
		}

		if (in_array('port', $parts) && array_key_exists('port', $parsed_url)) {
			$url_parts[] = ':' . $parsed_url['port'];
		}

		if (in_array('path', $parts) && array_key_exists('path', $parsed_url)) {
			$url_parts[] = $parsed_url['path'];
		}

		if (in_array('query', $parts) && array_key_exists('query', $parsed_url)) {
			$url_parts[] = '?' . $parsed_url['query'];
		}

		if (in_array('fragment', $parts) && array_key_exists('fragment', $parsed_url)) {
			$url_parts[] = '#' . $parsed_url['fragment'];
		}

		return implode('', $url_parts);
	}

}

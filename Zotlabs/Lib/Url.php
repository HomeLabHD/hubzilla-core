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
			'result' => unparse_url($parts)
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

}

<?php
/*
 * Forward compatibility with more recent PHP versions.
 *
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 *
 * This file contains functions that allow us to use convenient functions from
 * later PHP versions in earlier versions where these functions may not be
 * supported directly.
 */

if (!function_exists('array_find')) {

	// array_find is defined in PHP 8.4 or higher, so for earlier PHP versions we
	// define it here.
	function array_find(array $array, callable $callback): mixed {
		foreach ($array as $key => $entry) {
			if ($callback($entry, $key) === true) {
				return $entry;
			}
		}

		return null;
	}
}

if (!function_exists('array_first')) {

	// array_first is defined in PHP 8.5 and later, so for earlier PHP versions
	// we define it here
	function array_first(array $array): mixed {
		return empty($array) ? null : $array[0];
	}
}

<?php
/**
 * Helper functions for getting info about the observer.
 *
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

/**
 * Get the unique hash identifying the current observer.
 *
 * Observer can be a local or remote channel.
 *
 * @return string Unique hash of observer, otherwise empty string if no
 *		observer
 */
function get_observer_hash() {
	$observer = App::get_observer();
	if (is_array($observer)) {
		return $observer['xchan_hash'];
	}

	return '';
}

/**
 * Get the guid of the current observer.
 *
 * Observer can be a local or remote channel.
 *
 * @return string The GUID of the observer, otherwise empty string if no
 *		observer
 */
function get_observer_guid() {
	$observer = App::get_observer();
	if (is_array($observer)) {
		return $observer['xchan_guid'];
	}

	return '';
}

/**
 * Get the name of the current observer.
 *
 * Observer can be a local or remote channel.
 *
 * @return string The name of the observer, otherwise empty string if no
 *		observer
 */
function get_observer_name() {
	$observer = App::get_observer();
	if (is_array($observer)) {
		return $observer['xchan_name'];
	}

	return '';
}

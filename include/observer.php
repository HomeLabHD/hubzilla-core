<?php
/**
 * Helper functions for getting info about the observer.
 *
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 *
 * The _observer_ in Hubzilla is the channel visiting the site in the current
 * session. This could be a local channel, or a remote channel logged in via
 * OpenWebAuth.
 *
 * If the observer is not set, or empty, this indicates an unauthenticated
 * visitor, which may mean a visitor from another site that don't support, or
 * has not enabled OpenWebAuth.
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

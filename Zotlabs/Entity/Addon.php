<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Entity;

use DBA;
use DateTimeImmutable;
use PDO;

/**
 * Objects of this class represents an addon.
 */
class Addon extends Entity
{
	protected static string $TABLE_NAME = "addon";

	/**
	 * Construct a new Addon object.
	 *
	 * Marked private to prevent it from being invoked outside the class.
	 */
	private function  __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly bool $installed,
		public readonly bool $admin,
		public readonly bool $hidden,
		public readonly DateTimeImmutable $timestamp
	) { }

	/**
	 * Get an addon registered in the database by name.
	 */
	public static function getByName(string $name): ?self
	{
		return array_first(self::selectAll(['aname' => $name]));
	}

	/**
	 * Get a list of all installed addons from the database
	 */
	public static function getInstalledAddons(): array
	{
		return self::selectAll(['installed' => 1]);
	}

	/**
	 * Install an addon by running the install function and registering the
	 * addon in the database.
	 */
	public static function install(string $plugin): void
	{
		self::loadAddonFile($plugin);

		$addon = self::getByName($plugin);
		if(! $addon) {
			$admin = function_exists($plugin . '_plugin_admin');
			$hidden = file_exists(self::addonPath($plugin) . '/.hidden');
			$t = new DateTimeImmutable("@" . filemtime(self::addonPath($plugin) . "/{$plugin}.php"));

			$addon = new Addon(0, $plugin, true, $admin, $hidden, $t);
			$addon->save();
		}

		$addon->callAddonFunc('install');
		$addon->load();
	}

	/**
	 * Load and initialize the addon.
	 *
	 * This ensures the addon code is loaded, and then tries to invoke the
	 * addon load function if it exists.
	 */
	public function load(): void
	{
		self::loadAddonFile($this->name);
		$this->callAddonFunc('load');
	}

	/**
	 * Unload the addon.
	 *
	 * Calls the addon unload function if it exists, to let the addon clean up
	 * and deregister any hooks or routes it has registered.
	 */
	public function unload(): void
	{
		$this->callAddonFunc('unload');
	}

	/**
	 * Uninstall the addon.
	 *
	 * Unloads and then calls the addon uninstall function to let the addon do
	 * further cleanup before removing the addon from the database addon table.
	 */
	public function uninstall(): void
	{
		$this->unload();
		$this->callAddonFunc('uninstall');
		$this->delete(['aname' => $this->name]);
	}

	/**
	 * Save the addon information to the database.
	 */
	public function save(): void
	{
		$this->insert([
			'aname' => $this->name,
			'installed' => intval($this->installed),
			'tstamp' => intval($this->timestamp->getTimeStamp()),
			'plugin_admin' => intval($this->admin),
			'hidden' => intval($this->hidden),
		]);
	}

	/**
	 * A helper to call public addon functions of the form
	 * `<myaddon>_<funcname>()`.
	 */
	private function callAddonFunc(string $funcname): void
	{
		$f = "{$this->name}_{$funcname}";
		if(function_exists($f)) {
			$f();
		}
	}

	/**
	 * Creates an addon object from a row in the database.
	 */
	protected static function fromDbRow(array $row): static
	{
		return new Addon(
			$row['id'],
			$row['aname'],
			$row['installed'],
			$row['plugin_admin'],
			$row['hidden'],
			new DateTimeImmutable("@{$row['tstamp']}")
		);
	}

	private static function addonPath(string $plugin): string
	{
		return "addon/{$plugin}";
	}

	private static function loadAddonFile(string $plugin): void
	{
		include_once(self::addonPath($plugin) . "/{$plugin}.php");
	}
}

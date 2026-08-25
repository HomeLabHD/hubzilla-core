<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Entity;

use Zotlabs\Entity\Addon;
use Zotlabs\Tests\Unit\UnitTestCase;

class AddonTest extends UnitTestCase
{
	public function testGetAddonByName(): void
	{
		Addon::install('baseapps');
		$addon = Addon::getByName('baseapps');

		$this->assertNotEquals(0, $addon->id);
		$this->assertEquals('baseapps', $addon->name);
		$this->assertTrue($addon->installed);
	}

	public function testGetInstalledAddons(): void
	{
		Addon::install('baseapps');
		Addon::install('mdpost');

		$addons = Addon::getInstalledAddons();

		$this->assertIsArray($addons);

		$this->assertCount(2, $addons);

		$pluginNames = array_map(fn ($addon) => $addon->name, $addons);

		$this->assertContains('mdpost', $pluginNames);
		$this->assertContains('baseapps', $pluginNames);
	}

	public function testUninstallAddon(): void
	{
		Addon::install('baseapps');
		Addon::install('mdpost');

		$addon = Addon::getByName('baseapps');
		$addon->uninstall();

		$addons = Addon::getInstalledAddons();

		$this->assertIsArray($addons);

		$this->assertCount(1, $addons);

		$pluginNames = array_map(fn ($addon) => $addon->name, $addons);

		$this->assertContains('mdpost', $pluginNames);
		$this->assertNotContains('baseapps', $pluginNames);
	}
}

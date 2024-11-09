<?php
/*
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Thumbs;

use Zotlabs\Thumbs\Epubthumb;
use Zotlabs\Tests\Unit\UnitTestCase;

class EpubthumbTest extends UnitTestCase {
	function testEpubThumbMatch(): void {
		$thumbnailer = new Epubthumb();

		$this->assertTrue($thumbnailer->Match('application/epub+zip'));
		$this->assertFalse($thumbnailer->Match('application/zip'));
	}
}

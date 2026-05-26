<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Photo;

use PHPUnit\Framework\TestCase;
use Zotlabs\Photo\PhotoImagick;

class PhotoMagickTest extends TestCase
{
	public function testSupportedFormats(): void {
		$ph = new PhotoImagick(null, null);

		$types = $ph->supportedTypes();

		$this->assertEquals('webp', $types['image/webp']);
		$this->assertEquals('avif', $types['image/avif']);
		$this->assertEquals('gif', $types['image/gif']);
		$this->assertEquals('jpg', $types['image/jpeg']);
		$this->assertEquals('png', $types['image/png']);
	}

	public function testInitializingWithImage(): void {
		$data = file_get_contents('images/hz-16.png');

		// If no type is given, make it a jpeg, regardless of input format.
		$ph = new PhotoImagick($data);
		$this->assertEquals('image/jpeg', $ph->getType());

		$formats = $ph->supportedTypes();

		// When a type is givem the image should be given that type.
		foreach($formats as $format => $ext) {
			$ph = new PhotoImagick($data, $format);
			$this->assertEquals($format, $ph->getType());
		}
	}
}

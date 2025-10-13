<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Photo;

use PHPUnit\Framework\Attributes\TestWith;
use Zotlabs\Photo\ImageQuality;
use Zotlabs\Tests\Unit\UnitTestCase;

class ImageQualityTest extends UnitTestCase
{
	private const DEFAULT_VALUE = [
		'image/jpg' => JPEG_QUALITY,
		'image/png' => PNG_QUALITY,
		'image/avif' => AVIF_QUALITY,
		'image/webp' => WEBP_QUALITY,
	];

	#[TestWith(['image/jpg', 55])]
	#[TestWith(['image/jpg', 100])]
	#[TestWith(['image/png', 8])]
	#[TestWith(['image/png', 0])]
	#[TestWith(['image/avif', 99])]
	#[TestWith(['image/webp', 1])]
	public function testConstructWithValidValues(string $mimeType, int $value): void {
		$q = new ImageQuality($mimeType, $value);
		$this->assertEquals($value, $q->value);
	}

	#[TestWith(['image/jpg', 0])]
	#[TestWith(['image/jpg', 101])]
	#[TestWith(['image/png', -1])]
	#[TestWith(['image/png', 11])]
	#[TestWith(['image/avif', 0])]
	#[TestWith(['image/avif', 101])]
	#[TestWith(['image/webp', 0])]
	#[TestWith(['image/webp', 101])]
	public function testConstructWithOutOfBoundsValueReplacedWithDefault(
		string $mimeType, int $value
	): void {
		$q = new ImageQuality($mimeType, $value);
		$this->assertEquals(self::DEFAULT_VALUE[$mimeType], $q->value);
	}
}

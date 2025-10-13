<?php
/*
 * SPDX-FileCopyrightText: 2025 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Photo;

/**
 * A class to represent image quality values.
 */
class ImageQuality
{
	readonly string $mimeType;
	readonly int $value;

	private const DEFAULT_VALUE = [
		'image/jpg' => JPEG_QUALITY,
		'image/png' => PNG_QUALITY,
		'image/avif' => AVIF_QUALITY,
		'image/webp' => WEBP_QUALITY,
	];

	public function __construct(string $mimeType, int $value) {
		[ $min, $max ] = $this->validRange($mimeType);

		if ($value < $min || $value > $max) {
			$value = self::DEFAULT_VALUE[$mimeType];
		}

		$this->value = $value;
		$this->mimeType = $mimeType;
	}

	/**
	 * Return the valid quality setting range for the given mime type.
	 *
	 * @param string $mimeType	The mime type whose range to return.
	 *
	 * @return The valid range as an array with two elements as
	 *		[ $min, $max ].
	 */
	public static function validRange(string $mimeType): array {
		// Max quality for PNG is 10, for all others it's 100.
		return $mimeType === 'image/png' ? [ 0, 10 ] : [ 1, 100 ];
	}
}

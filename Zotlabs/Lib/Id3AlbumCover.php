<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use Id3\Frame\ApicFrame;

class Id3AlbumCover extends ApicFrame
{
	public function __construct(ApicFrame $src)
	{
		$this->data = $src->data;
	}

	public function saveThumbnail(string $filename, int $width, int $height): void
	{
		$image = imagecreatefromstring($this->data);
		$dest = imagecreatetruecolor( $width, $height );
		$srcwidth = imagesx($image);
		$srcheight = imagesy($image);

		imagealphablending($dest, false);
		imagesavealpha($dest, true);
		imagecopyresampled($dest, $image, 0, 0, 0, 0, $width, $height, $srcwidth, $srcheight);

		imagejpeg($dest, $filename . '.thumb');
	}
}

<?php
// SPDX-FileCopyrightText: 2026 The Hubzilla Community
//
// SPDX-License-Identifier: MIT

namespace Zotlabs\Thumbs;

use Id3\Exception\NotCompliantException;
use Id3\Id3Parser;
use Zotlabs\Lib\Id3AlbumCover;

/**
 * Thumbnail generator for MP3 audio files.
 *
 * Extracts embedded cover art from the MP3 ID3 tag, and uses this as a
 * thumbnail if it exists.
 */
class Mp3audio {

	function Match($type) {
		return(($type === 'audio/mpeg') ? true : false );
	}

	/**
	 * Extract the embedded album cover as thumbnail if it exists.
	 *
	 * @param array $attach
	 *		Associative array describing the attachment.
	 * @param int $preview_style
	 *		Not used.
	 * @param int $height
	 *		The hight of the thumbnail in px.
	 * @param int $width
	 *		The width of the thumbnail in px.
	 */
	function Thumb($attach,$preview_style,$height = 300, $width = 300) {

		$file = dbunescbin($attach['content']);

		if (empty($file)) {
			return;
		}

		try {
			$id3 = new Id3Parser($file);

			$album_image = $id3->getAlbumImage();

			if ($album_image) {
				logger("extracting album cover from {$file}...", LOGGER_DEBUG);
				$photo = new Id3AlbumCover($album_image);
				$photo->saveThumbnail($file, $width, $height);
			} else {
				logger("{$file} does not contain an album cover", LOGGER_DEBUG);
			}
		} catch (NotCompliantException $ex) {
			logger("{$file} does not contain an ID3 tag.", LOGGER_DEBUG);
		}
	}
}


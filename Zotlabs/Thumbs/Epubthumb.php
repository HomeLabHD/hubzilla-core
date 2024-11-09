<?php

namespace Zotlabs\Thumbs;

use SebLucas\EPubMeta\EPub;
use GdImage;

/**
 * Thumbnail creation for epub files.
 */
class Epubthumb {

	/**
	 * Match for application/epub+zip.
	 *
	 * @param string $type MimeType
	 * @return boolean
	 */
	function Match(string $type): bool {
		return $type === 'application/epub+zip';
	}

	/**
	 * Create the thumbnail if the Epub has a cover.
	 *
	 * @param array $attach
	 * @param number $preview_style unused
	 * @param number $height (optional) default 300
	 * @param number $width (optional) default 300
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function Thumb($attach, $preview_style, $height = 300, $width = 300) {

		$file = dbunescbin($attach['content']);
		if (!$file) {
			return;
		}

		$image = $this->getCover($file);

		if ($image) {
			$srcwidth = imagesx($image);
			$srcheight = imagesy($image);

			$dest = imagecreatetruecolor($width, $height);
			imagealphablending($dest, false);
			imagesavealpha($dest, true);

			imagecopyresampled($dest, $image, 0, 0, 0, 0, $width, $height, $srcwidth, $srcheight);

			imagejpeg($dest, "{$file}.thumb");

			imagedestroy($image);
			imagedestroy($dest);
		}
	}

	private function getCover(string $filename): GdImage|false {
		$epub = new EPub($filename);
		$cover = $epub->getCover();

		if (! empty($cover)) {
			return imagecreatefromstring($cover);
		} else {
			return false;
		}
	}
}


<?php

namespace Zotlabs\Thumbs;

use Id3\Id3Parser;
use Zotlabs\Lib\Id3AlbumCover;

class Mp3audio {

	function Match($type) {
		return(($type === 'audio/mpeg') ? true : false );
	}

	function Thumb($attach,$preview_style,$height = 300, $width = 300) {

		$file = dbunescbin($attach['content']);
		if (empty($file)) {
			return;
		}

		$id3 = new Id3Parser($file);
		$photo = new Id3AlbumCover($id3->getAlbumImage());
		$photo->saveThumbnail($file, $width, $height);
	}
}


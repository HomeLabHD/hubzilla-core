<?php

namespace Zotlabs\Thumbs;

use DOMDocument;
use DOMElement;
use DOMXPath;
use GdImage;
use ZipArchive;

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
	 * @param int $preview_style unused
	 * @param int $height (optional) default 300
	 * @param int $width (optional) default 300
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
	 */
	function Thumb($attach, $preview_style, $height = 300, $width = 300): void {

		$file = dbunescbin($attach['content']);
		if (!$file) {
			return;
		}

		$image = $this->getCoverFromEpub($file);

		if ($image) {
			$srcwidth = imagesx($image);
			$srcheight = imagesy($image);

			$dest = imagecreatetruecolor($width, $height);
			imagealphablending($dest, false);
			imagesavealpha($dest, true);

			imagecopyresampled($dest, $image, 0, 0, 0, 0, $width, $height, $srcwidth, $srcheight);

			imagejpeg($dest, "{$file}.thumb");
		}
	}

	/**
	 * Fetch the cover from the epub archive, if it's present.
	 *
	 * There's a few limitations here: This will only work if the cover
	 * is a raster image of a supported format. SVG does not work, neither
	 * will other schemes sometimes used for cover/front page.
	 *
	 * @param string $filename	The local filename of the epub archive.
	 *
	 * @return GdImage|false	If a cover is found, it is returned as a
	 *	GdImage object. Otherwise return false.
	 */
	private function getCoverFromEpub(string $filename): GdImage|false {
		$epub = new ZipArchive();
		$rc = $epub->open($filename, ZipArchive::RDONLY);

		if ($rc !== true) {
			logger("Error opening file '{$filename}': rc = ${rc}.", LOGGER_DEBUG, LOG_DEBUG);
			return false;
		}

		$cover = false;
		$cover_name = $this->parseEpub($epub);
		if ($cover_name !== false) {
			$cover = $epub->getFromName($cover_name);
			if ($cover === false) {
				logger("File '{$cover_name}' not found in EPUB.", LOGGER_DEBUG, LOG_DEBUG);
			}
		}

		$epub->close();

		if ($cover !== false && !empty($cover)) {
			return imagecreatefromstring($cover);
		} else {
			return false;
		}
	}

	/**
	 * Parse the epub to find the path of the cover image.
	 *
	 * @param ZipArchive $epub	An opened epub ZipArchive.
	 *
	 * @return string|false		The path to the cover image or false.
	 */
	private function parseEpub(ZipArchive $epub): string|false {
		$packagePath = $this->getEpubPackagePath($epub);
		if ($packagePath !== false) {
			$package = $epub->getFromName($packagePath);
			if ($package === false || empty($package)) {
				logger("Package file '${packagePath}' not found in EPUB", LOGGER_DEBUG, LOG_DEBUG);
				return false;
			}

			$domdoc = new DOMDocument();
			$domdoc->loadXML($package);
			$xpath = new DOMXPath($domdoc);
			$xpath->registerNamespace("n", "http://www.idpf.org/2007/opf");
			$nodes = $xpath->query('/n:package/n:manifest/n:item[@properties="cover-image"]');

			if ($nodes->count() === 0) {
				logger('No cover found in EPUB manifest.', LOGGER_DEBUG, LOG_DEBUG);
				return false;
			}

			$node = $nodes->item(0);
			if ($node === null) {
				logger('No nodes in non-empty node list?', LOGGER_DEBUG, LOG_DEBUG);
				return false;
			}

			if (is_a($node, DOMElement::class)) {
				// The URL's in the package file is relative to the subdirectory
				// within the epub archive where it is located. See
				// https://www.w3.org/TR/epub-33/#sec-parsing-urls-metainf
				return dirname($packagePath) . '/' . $node->getAttribute('href');
			}
		}

		return false;
	}

	/**
	 * Locate the package file within the epub.
	 *
	 * The package file in an epub archive contains the manifest
	 * that again may contain a reference to the cover for the
	 * epub.
	 *
	 * @param ZipArchive $epub	An opened epub archive.
	 *
	 * @return string|false		The full pathname of the package file or false.
	 */
	private function getEpubPackagePath(ZipArchive $epub): string|false {
		//
		// The only mandatory known file within the archive is the
		// container file, so we fetch it to find the reference to
		// the package file.
		//
		// See: https://www.w3.org/TR/epub-33/#sec-container-metainf
		//
		$container = $epub->getFromName('META-INF/container.xml');

		if ($container === false || empty($container)) {
			logger('No container in archive, probably not an EPUB.', LOGGER_DEBUG, LOG_DEBUG);
			return false;
		}

		$domdoc = new DOMDocument();
		$domdoc->loadXML($container);
		$nodes = $domdoc->getElementsByTagName('rootfile');

		if ($nodes->count() == 0) {
			logger('EPUB rootfile not found, is this an epub?', LOGGER_DEBUG, LOG_DEBUG);
			return false;
		}

		$packageNode = $nodes->item(0);
		if ($packageNode === null || !is_a($packageNode, DOMElement::class)) {
			logger('EPUB rootfile element missing or invalid.', LOGGER_DEBUG, LOG_DEBUG);
			return false;
		}

		$packagePath = $packageNode->getAttribute('full-path');
		$packageMediaType = $packageNode->getAttribute('media-type');

		if (empty($packagePath) || $packageMediaType !== 'application/oebps-package+xml') {
			logger('EPUB package path missing or incorrect media type.', LOGGER_DEBUG, LOG_DEBUG);
			return false;
		}

		return $packagePath;
	}
}


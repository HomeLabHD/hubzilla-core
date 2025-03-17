<?php
/*
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Thumbs;

use PHPUnit\Framework\Attributes\{AfterClass, Before, BeforeClass};
use Zotlabs\Thumbs\Epubthumb;
use Zotlabs\Tests\Unit\UnitTestCase;

use ZipArchive;

class EpubthumbTest extends UnitTestCase {
	private const TMPDIR = __DIR__ . '/tmp';

	private Epubthumb	$thumbnailer;

	/**
	 * Create a temp dir to use for the tests in this class.
	 */
	#[BeforeClass]
	static function setupTmpDir(): void {
		if (!is_dir(self::TMPDIR)) {
			mkdir(self::TMPDIR);
		}
	}

	/**
	 * Clean up and remove the temp dir after the tests.
	 */
	#[AfterClass]
	static function cleanupTmpDir(): void {
		$files = scandir(self::TMPDIR);
		if ($files !== false) {
			foreach($files as $f) {
				if ($f[0] !== '.') {
					unlink(self::TMPDIR . '/' . $f);
				}
			}
		}
		rmdir(self::TMPDIR);
	}

	/**
	 * Create the thumbnailer object for tests.
	 *
	 * This is run before each test, so that each test has it's own
	 * instance of the thumbnailer.
	 */
	#[Before]
	function createThumbnailer(): void {
		$this->thumbnailer = new Epubthumb();
	}

	/*
	 * Tests
	 */

	public function testEpubThumbMatch(): void {
		$this->assertTrue($this->thumbnailer->Match('application/epub+zip'));
		$this->assertFalse($this->thumbnailer->Match('application/zip'));
	}

	public function testNoThumbnailCreatedForFileThatDontExist(): void {
		$this->checkCreateThumbnail(self::TMPDIR . '/nonexisting.epub', false);
	}

	public function testNoThumbnailCreatedIfNotAZipArchive(): void {
		$filename = self::TMPDIR . '/notazip.epub';

		file_put_contents($filename, 'This is not a ZIP file!');

		$this->checkCreateThumbnail($filename, false);
	}

	public function testNoThumbnailCreatedIfInvalidEpub(): void {
		$filename = self::TMPDIR . '/nocontainer.epub';

		$epub = new ZipArchive();
		$epub->open($filename, ZipArchive::CREATE);
		$epub->addFromString('somefile.txt', 'It was a dark an stormy night...');
		$epub->close();

		$this->checkCreateThumbnail($filename, false);
	}

	public function testNoThumbnailCreatedIfCoverFileMissing(): void {
		$filename = self::TMPDIR . '/good.epub';

		$epub = new ZipArchive();
		$epub->open($filename, ZipArchive::CREATE);
		$this->addEpubContainer($epub);
		$this->addEpubPackage($epub);
		$epub->close();

		$this->checkCreateThumbnail($filename, false);
	}

	public function testCreateCoverFromEpub(): void {
		$filename = self::TMPDIR . '/good.epub';

		$epub = new ZipArchive();
		$epub->open($filename, ZipArchive::CREATE);
		$this->addEpubContainer($epub);
		$this->addEpubPackage($epub);
		$epub->addFile(PROJECT_BASE . '/images/red-koala.png', 'EPUB/cover.png');
		$epub->close();

		$this->checkCreateThumbnail($filename, true);
	}

	/*
	 * Helper functions
	 */

	private function checkCreateThumbnail(string $filename, bool $expectThumbnail): void {
		$attach = [ 'content' => $filename ];
		$this->thumbnailer->Thumb($attach, 0);

		$this->assertEquals($expectThumbnail, file_exists($filename . '.thumb'));
	}

	private function addEpubContainer(ZipArchive $epub): void {
		$xml = <<<XML
			<?xml version="1.0" encoding="UTF-8"?>
			<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
			  <rootfiles>
				<rootfile full-path="EPUB/package.opf" media-type="application/oebps-package+xml"/>
			  </rootfiles>
			</container>
			XML;

		$epub->addEmptyDir('META-INF');
		$epub->addFromString('META-INF/container.xml', $xml);
	}

	private function addEpubPackage(ZipArchive $epub): void {
		$xml = <<<XML
			<?xml version="1.0" encoding="UTF-8"?>
			<package xmlns="http://www.idpf.org/2007/opf" version="3.0" unique-identifier="pub-identifier">
				<manifest>
					<item
						properties="cover-image"
						id="ci"
						href="cover.png"
						media-type="image/png" />
				</manifest>
			</package>
			XML;

		$epub->addEmptyDir('EPUB');
		$epub->addFromString('EPUB/package.opf', $xml);
	}
}

<?php

namespace Zotlabs\Tests\Unit\includes;

use Zotlabs\Tests\Unit\UnitTestCase;

/**
 * @brief Unit Test cases for include/photo/photo_driver.php file.
 */
class PhotodriverTest extends UnitTestCase {

	public function testPhotofactoryReturnsNullForUnsupportedType() {
		$photo = \photo_factory('', 'image/bmp');
		$this->assertNull($photo);
	}

	public function testPhotofactoryReturnsPhotogdIfConfigIgnore_imagickIsSet() {
		\Zotlabs\Lib\Config::Set('system', 'ignore_imagick', true);

		$photo = \photo_factory(file_get_contents('images/hz-16.png'), 'image/png');
		$this->assertInstanceOf('Zotlabs\Photo\PhotoGd', $photo);
	}

	// Helper to create a temporary image file
	private function createTempImage($type = 'jpeg'): string
	{
		$tmp = tempnam(sys_get_temp_dir(), 'img');
		switch ($type) {
			case 'png':
				$im = imagecreatetruecolor(10, 10);
				imagepng($im, $tmp);
				break;
			case 'jpeg':
			default:
				$im = imagecreatetruecolor(10, 10);
				imagejpeg($im, $tmp);
				break;
		}
		return $tmp;
	}

	public function testGuessImageTypeFromRawData()
	{
		$filename = 'irrelevant';
		$data = [
			'body' => file_get_contents($this->createTempImage('jpeg'))
		];
		$result = guess_image_type($filename, $data);
		$this->assertEquals('image/jpeg', $result);
	}

	public function testGuessImageTypeFromLocalFile()
	{
		$file = $this->createTempImage('png');
		$result = guess_image_type($file);
		$this->assertEquals('image/png', $result);
		unlink($file);
	}

	public function testGuessImageTypeFromHeaders()
	{
		$filename = 'irrelevant';
		$data = [
			'header' => "Content-Type: image/jpeg\nOther: value"
		];
		$result = guess_image_type($filename, $data);
		$this->assertEquals('image/jpeg', $result);
	}

	public function testGuessImageTypeUnknownTypeReturnsNull()
	{
		$filename = 'not_an_image.txt';
		$data = [
			'body' => 'not an image'
		];
		$result = guess_image_type($filename, $data);
		$this->assertNull($result);
	}
}

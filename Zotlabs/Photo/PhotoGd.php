<?php

namespace Zotlabs\Photo;

use Zotlabs\Lib\Config;

/**
 * @brief GD photo driver.
 *
 */
class PhotoGd extends PhotoDriver {

	/**
	 * {@inheritDoc}
	 *
	 * @see \Zotlabs\Photo\PhotoDriver::supportedTypes()
	 */
	public function supportedTypes() {

		$t = [];

		$t['image/jpeg'] = 'jpg';
		if(\imagetypes() & IMG_PNG)
			$t['image/png'] = 'png';
		if(\imagetypes() & IMG_GIF)
			$t['image/gif'] = 'gif';
		if(\imagetypes() & IMG_WEBP)
			$t['image/webp'] = 'webp';
		if(\imagetypes() & IMG_AVIF)
			$t['image/avif'] = 'avif';

		return $t;
	}

	/**
	 * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
	 */
	protected function load($data, $type) {
		$this->valid = false;
		if(! $data)
			return;

		$this->image = imagecreatefromstring($data);
		if($this->image !== false) {
			$this->valid  = true;
			$this->setDimensions();
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
		}
	}

	protected function setDimensions() {
		$this->width  = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	/**
	 * @brief GD driver does not preserve EXIF, so not need to clear it.
	 *
	 * @return void
	 */
	public function clearexif() {
		return;
	}

	protected function destroy() {
		if($this->is_valid()) {
			imagedestroy($this->image);
		}
	}

	/**
	 * @brief Return a PHP image resource of the current image.
	 *
	 * @see \Zotlabs\Photo\PhotoDriver::getImage()
	 *
	 * @return boolean|resource
	 */
	public function getImage() {
		if(! $this->is_valid())
			return false;

		return $this->image;
	}

	public function doScaleImage($dest_width, $dest_height) {

		$dest = imagecreatetruecolor($dest_width, $dest_height);
		$width = imagesx($this->image);
		$height = imagesy($this->image);

		imagealphablending($dest, false);
		imagesavealpha($dest, true);
		if($this->type == 'image/png')
			imagefill($dest, 0, 0, imagecolorallocatealpha($dest, 0, 0, 0, 127)); // fill with alpha

		imagecopyresampled($dest, $this->image, 0, 0, 0, 0, $dest_width, $dest_height, $width, $height);
		if($this->image)
			imagedestroy($this->image);

		$this->image = $dest;
		$this->setDimensions();
	}

	public function rotate($degrees) {
		if(! $this->is_valid())
			return false;

		$this->image = imagerotate($this->image, $degrees, 0);
		$this->setDimensions();
	}

	public function flip($horiz = true, $vert = false) {
		if(! $this->is_valid())
			return false;

		$w = imagesx($this->image);
		$h = imagesy($this->image);
		$flipped = imagecreate($w, $h);
		if($horiz) {
			for($x = 0; $x < $w; $x++) {
				imagecopy($flipped, $this->image, $x, 0, $w - $x - 1, 0, 1, $h);
			}
		}
		if($vert) {
			for($y = 0; $y < $h; $y++) {
				imagecopy($flipped, $this->image, 0, $y, 0, $h - $y - 1, $w, 1);
			}
		}
		$this->image = $flipped;
		$this->setDimensions(); // Shouldn't really be necessary
	}

	public function cropImageRect($maxx, $maxy, $x, $y, $w, $h) {
		if(! $this->is_valid())
			return false;

		$dest = imagecreatetruecolor($maxx, $maxy);
		imagealphablending($dest, false);
		imagesavealpha($dest, true);
		if($this->type == 'image/png')
			imagefill($dest, 0, 0, imagecolorallocatealpha($dest, 0, 0, 0, 127)); // fill with alpha

		imagecopyresampled($dest, $this->image, 0, 0, $x, $y, $maxx, $maxy, $w, $h);
		if($this->image)
			imagedestroy($this->image);

		$this->image = $dest;
		$this->setDimensions();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @see \Zotlabs\Photo\PhotoDriver::imageString()
	 */
	public function imageString() {

		if(! $this->is_valid())
			return false;

		ob_start();

		switch($this->getType()){

			case 'image/avif':
				imageavif($this->image, null, $this->getQuality()->value);
			    break;

			case 'image/png':
				imagepng($this->image, null, $this->getQuality()->value);
				break;

			case 'image/webp':
				imagewebp($this->image, null, $this->getQuality()->value);
			    break;

			// gd can lack imagejpeg(), but we verify during installation it is available
			case 'image/jpeg':
			default:
				imagejpeg($this->image, null, $this->getQuality()->value);
				break;
		}

		$string = ob_get_contents();
		ob_end_clean();

		return $string;
	}

	/**
	 * Return the default image quality for the given mime type.
	 *
	 * If the setting has been overridden in the database, we use that value,
	 * otherwise the hardcoded defaults.
	 *
	 * @return ImageQuality		The image quality value for the current mime type.
	 */
	private function getQuality(): ImageQuality {
		$key = match($this->getType()) {
			'image/avif' => 'avif_quality',
			'image/jpeg' => 'jpeg_quality',
			'image/png' => 'png_quality',
			'image/webp' => 'webp_quality',
		};

		return new ImageQuality($this->getType(), Config::Get('system', $key));
	}

}

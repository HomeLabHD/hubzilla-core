<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\includes;

use App;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use Zotlabs\Tests\Unit\UnitTestCase;

require_once 'include/opengraph.php';

#[BackupStaticProperties('App')]
class OpenGraphTest extends UnitTestCase
{
	public function testWithEmptyItem(): void {
		App::$profile['about'] = false;
		$channel = [
			'channel_name' => 'test',
			'xchan_photo_l' => z_root() . '/photos/1',
			'xchan_photo_mimetype' => 'image/jpeg',
		];

		opengraph_add_meta(null, $channel);

		$this->assertMetaOGTag('og:title', 'test');
		$this->assertMetaOGTag('og:image', z_root() . '/photos/1');
		$this->assertMetaOGTag('og:image:type', 'image/jpeg');
		$this->assertMetaOGTag('og:description', 'This is the home page of test.');
		$this->assertMetaOGTag('og:type', 'profile');
	}

	public function testWithPostItem(): void {
		App::$profile['about'] = false;
		$channel = [
			'channel_name' => 'test',
			'xchan_photo_l' => z_root() . '/photos/1',
			'xchan_photo_mimetype' => 'image/jpeg',
		];

		$item = [
			'title' => 'The post title',
			'body' => '[zmg=' . z_root() . '/some_image.jpg]An image with alt text[/zmg]',
			'summary' => 'A summary of the post',
		];

		opengraph_add_meta($item, $channel);

		$this->assertMetaOGTag('og:title', 'The post title');
		$this->assertMetaOGTag('og:image', z_root() . '/some_image.jpg');

		//
		// Image type is empty because `guess_image_type()` won't be able
		// to locate the image, and we can't mock the function as it's not
		// called from a namespace.
		//
		$this->assertMetaOGTag('og:image:type', '');

		$this->assertMetaOGTag('og:description', 'A summary of the post');
		$this->assertMetaOGTag('og:type', 'article');
	}

	private function assertMetaOGTag(string $tag, string $content): void {
		$this->assertStringContainsString(
			"<meta property=\"{$tag}\" content=\"{$content}\">",
		   	App::$page['htmlhead']);
	}
}

<?php

namespace Zotlabs\Tests\Unit\includes;

use Zotlabs\Tests\Unit\UnitTestCase;

require_once('include/feedutils.php');

/**
 * @brief Unit Test case for include/feedutils.php file.
 */
class FeedutilsTest extends UnitTestCase {

	public function test_normalise_id() {
		$this->assertEquals('id', normalise_id('id'));
		$this->assertEquals('id', normalise_id('X-ZOT:id'));
		$this->assertEquals('id id2', normalise_id('X-ZOT:id X-ZOT:id2'));
		$this->assertEmpty(normalise_id(''));
	}

	public function test_encode_rel_links() {
		// invalid params return empty array
		$this->assertEquals([], encode_rel_links('string'));
		$this->assertEquals([], encode_rel_links([]));

		$b = ['attribs' => ['' => [
				'rel' => 'rel_value',
				'type' => 'type_value',
				'href' => 'href_value',
				'length' => 'length_value',
				'title' => 'title_value'
		]]];
		$blink1 = ['link1' => $b];
		$bresult[] = $b['attribs'][''];
		$this->assertEquals($bresult, encode_rel_links($blink1));
	}

/*	public function test_encode_rel_links_fail() {
		$a = [ 'key' => 'value'];
		$this->assertFalse(encode_rel_links($a));
		//Illegal string offset 'attribs'
	}*/

	/**
	 * @uses ::xmlify
	 */
	public function test_atom_author() {
		$this->assertEquals('', atom_author('', 'nick', 'name', 'uri', 72, 72, 'png', 'photourl'));

		$expected = '<tag>
  <id>uri</id>
  <name>nick</name>
  <uri>uri</uri>
  <link rel="photo"  type="png" media:width="72" media:height="72" href="http://photourl" />
  <link rel="avatar" type="png" media:width="72" media:height="72" href="http://photourl" />
  <poco:preferredUsername>nick</poco:preferredUsername>
  <poco:displayName>name</poco:displayName>
</tag>';

		$this->assertAtomAuthorMatches(
			$expected,
			atom_author('tag', 'nick', 'name', 'uri', 72, 72, 'png', 'http://photourl')
		);
	}

	/**
	 * @uses ::xmlify
	 */
	public function test_atom_render_author() {
		$xchan = [
				'xchan_addr' => 'chan@hub',
				'xchan_url' => 'http://hub',
				'xchan_name' => 'Chan',
				'xchan_photo_l' => 'http://hub/img',
				'xchan_photo_mimetype' => 'mimetype'
		];
		// There is no input validation in atom_render_author
		//$this->assertEquals('', atom_render_author('', $xchan));

		$a = '<tag>
  <as:object-type>http://activitystrea.ms/schema/1.0/person</as:object-type>
  <id>http://hub</id>
  <name>chan</name>
  <uri>http://hub</uri>
  <link rel="alternate" type="text/html" href="http://hub" />
  <link rel="photo"  type="mimetype" media:width="300" media:height="300" href="http://hub/img" />
  <link rel="avatar" type="mimetype" media:width="300" media:height="300" href="http://hub/img" />
  <poco:preferredUsername>chan</poco:preferredUsername>
  <poco:displayName>Chan</poco:displayName>
</tag>';

		$this->assertAtomAuthorMatches($a, atom_render_author('tag', $xchan));
	}

	/**
	 * Helper method to assert that the generated author tag matches
	 * what we expect.
	 *
	 * Calling `assertXmlStringEqualsXmlString` directly on the fragments
	 * does not work anymore in PHPUnit >= 10.x, as t will throw an XMLException
	 * because of undefined namespaces.
	 *
	 * To overcome that we wrap the generated tags in the proper template,
	 * and compare the fully generated XML from the template instead.
	 *
	 * @param string $expected	The expected author XML fragment.
	 * @param string $actual	The actually generated authr XML fragment.
	 */
	private function assertAtomAuthorMatches(string $expected, string $actual): void {

		// Make sure the template engine is initialized before we try to render
		// the template.
		//
		// This may be problematic, as it will compile the template into the same
		// directory as the site. Assuming that nobody is crazy enough to run the
		// test suite in a production server, it should probably be fine for now.
		$smarty = new \Zotlabs\Render\SmartyTemplate();
		\App::register_template_engine(get_class($smarty));

		$feed_template = get_markup_template('atom_feed.tpl');
		$expected_xml = replace_macros($feed_template, [
			'$version'       => 42,
			'$generator'     => 'Hubzilla test',
			'$generator_uri' => 'https://hubzilla.test',
			'$feed_id'       => 'test_channel',
			'$feed_title'    => 'Test channel',
			'$feed_updated'  => 'Sometime',
			'$author'        => $expected,
			'$owner'         => $expected,
			'$profile_page'  => xmlify('https://hubzilla.test/channel/test'),
		]);

		$expected_xml .= '</feed>';

		$actual_xml = replace_macros($feed_template, [
			'$version'       => 42,
			'$generator'     => 'Hubzilla test',
			'$generator_uri' => 'https://hubzilla.test',
			'$feed_id'       => 'test_channel',
			'$feed_title'    => 'Test channel',
			'$feed_updated'  => 'Sometime',
			'$author'        => $actual,
			'$owner'         => $actual,
			'$profile_page'  => xmlify('https://hubzilla.test/channel/test'),
		]);

		$actual_xml .= '</feed>';

		$this->assertXmlStringEqualsXmlString($expected_xml, $actual_xml);
	}
}

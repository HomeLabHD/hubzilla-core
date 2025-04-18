<?php
namespace Zotlabs\Tests\Unit\Lib;

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\MessageFilter;

class MessageFilterTest extends UnitTestCase {

	/**
	 * Test the `evaluate` function.
	 *
	 * @dataProvider evaluate_provider
	 */
	public function test_evaluate(string $incl, string $excl, bool $result) : void {
		$item = [
			'title' => '',
			'body' => "A grasshopper spent the summer hopping about in the sun and singing to his heart's content. One day, an ant went hurrying by, looking very hot and weary.\r\n#story #grasshopper #ant",
			'term' => [
				['ttype' => TERM_HASHTAG, 'term' => 'story'],
				['ttype' => TERM_HASHTAG, 'term' => 'grasshopper'],
				['ttype' => TERM_HASHTAG, 'term' => 'ant']
			],
			'verb' => 'Create',
			'obj_type' => 'Note',
			'obj' => [
				'type' => 'Note',
				'attributedTo' => 'https://example.com/users/test',
				'summary' => null,
				'content' => "A grasshopper spent the summer hopping about in the sun and singing to his heart's content. One day, an ant went hurrying by, looking very hot and weary.\r\n#story #grasshopper #ant",
				'sensitive' => false
			],
			'item_private' => 0,
			'item_thread_top' => 1
		];

		$this->assertEquals($result, MessageFilter::evaluate($item, $incl, $excl));
	}

	public static function evaluate_provider() : array {
		return [
			'body contains incl' => [
				'summer',
				'',
				true
			],
			'body contains excl' => [
				'',
				'summer',
				false
			],
			'body contains word hopper (starting with a space) in excl using regex' => [
				'',
				'/ hopper/',
				true
			],
			'lang=en in incl' => [
				'lang=en',
				'',
				true
			],
			'lang=en in excl' => [
				'',
				'lang=en',
				false
			],
			'lang=de in incl' => [
				'lang=de',
				'',
				false
			],
			'lang=de in excl' => [
				'',
				'lang=de',
				true
			],
			'hashtag in incl' => [
				'#grasshopper',
				'',
				true
			],
			'hashtag in excl' => [
				'',
				'#grasshopper',
				false
			],
			'any hashtag in excl' => [
				'',
				'#*',
				false
			],
			'item.body contains substring hopper in excl' => [
				'',
				'?body ~= hopper',
				false
			],
			'item.verb == Announce in excl' => [
				'',
				'?verb == Announce',
				true
			],
			'item.verb != Announce in incl' => [
				'?verb != Announce',
				'',
				true
			],
			'combined body contains word and item.verb == Announce in excl' => [
				'',
				"summer\r\n?verb == Announce",
				false
			],
			'item.item_thread_top == 1 in excl' => [
				'',
				"?item_thread_top == 1",
				false
			],
			'combined item_private == 0 and item.item_thread_top == 1 in excl' => [
				'',
				"?item_private == 0\r\n?item_thread_top == 1",
				false
			],
			'item.item_private < 1 in excl' => [
				'',
				"?item_private < 1",
				false
			],
			'item.item_thread_top = 1 and item.item_private > 0 in excl' => [
				'',
				"?item_thread_top == 1 && ?item_private > 0 ",
				true
			],
			'item.item_thread_top = 1 and item.item_private < 1 in excl' => [
				'',
				"?item_thread_top == 1 && ?item_private < 1 ",
				false
			],
			'item.item_thread_top = 1 or item.item_private = 0 in excl' => [
				'',
				"?item_thread_top == 1 && ?item_private == 0",
				false
			],
			'item.item_private < 1 and item.item_thread_top = 1 in excl' => [
				'',
				"?item_private < 1 && ?item_thread_top == 1",
				false
			],
			'item.item_private < 1 and item.item_thread_top = 0 in excl' => [
				'',
				"?item_private < 1 && ?item_thread_top == 0",
				true
			],
			'combined item.verb = Create, item.item_private < 1 and item.item_thread_top = 0 in excl' => [
				'',
				"?verb == Create\r\n?item_private < 1 && ?item_thread_top == 1",
				false
			],
			'item.obj contains value Note in incl' => [
				'?obj {} Note',
				'',
				true
			],
			'item.obj contains key type in incl' => [
				'?obj {*} type',
				'',
				true
			],
			'obj.type = Note in incl' => [
				'?+type == Note',
				'',
				true
			],
			'obj.sensitive = true in incl' => [
				'?+sensitive',
				'',
				false
			],
			'obj.sensitive != false in incl' => [
				'?+!sensitive',
				'',
				true
			],
		];
	}
}

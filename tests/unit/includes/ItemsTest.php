<?php
/**
 * tests function from include/items.php
 *
 * @package test.util
 */

use Zotlabs\Tests\Unit\UnitTestCase;

class ItemsTest extends UnitTestCase {
    /**
     * Data provider for item_forwardable function.
     *
     * @return array
     */
    public static function itemForwardableDataProvider()
    {
        return [
            // Test case: item is unpublished
            [
                [
                    'item_unpublished' => 1,
                    'item_delayed' => 0,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => '',
                    'author' => ['xchan_network' => '']
                ],
                false // Expected result
            ],
            // Test case: item is delayed
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 1,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => '',
                    'author' => ['xchan_network' => '']
                ],
                false
            ],
            // Test case: item is blocked
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 0,
                    'item_blocked' => 1,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => '',
                    'author' => ['xchan_network' => '']
                ],
                false
            ],
            // Test case: verb is 'Follow' (forbidden verb)
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 0,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Follow',
                    'postopts' => '',
                    'author' => ['xchan_network' => '']
                ],
                false
            ],
            // Test case: postopts contains 'nodeliver'
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 0,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => 'nodeliver',
                    'author' => ['xchan_network' => '']
                ],
                false
            ],
            // Test case: actor's network is 'rss' (restricted network)
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 0,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => '',
                    'author' => ['xchan_network' => 'rss']
                ],
                false
            ],
            // Test case: no conditions met (should forward)
            [
                [
                    'item_unpublished' => 0,
                    'item_delayed' => 0,
                    'item_blocked' => 0,
                    'item_hidden' => 0,
                    'item_restrict' => 0,
                    'verb' => 'Create',
                    'postopts' => '',
                    'author' => ['xchan_network' => 'other']
                ],
                true
            ]
        ];
    }

    /**
     * Test item_forwardable with various data.
     *
     * @dataProvider itemForwardableDataProvider
     */
    public function testItemForwardable($item, $expected)
    {
        $this->assertSame($expected, item_forwardable($item));
    }

}



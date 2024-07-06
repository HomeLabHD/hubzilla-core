<?php
/**
 * Unit tests for the `call_hooks` function, located in include/plugin.php.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit;

use PHPUnit\Framework\Attributes\BackupStaticProperties;
use App;

#[BackupStaticProperties(App::class)]
class CallHooksTest extends UnitTestCase {

	/**
	 * Test using a freestanding function as callback.
	 *
	 * @SuppressWarnings(PHPMD.EvalExpression)
	 */
	public function test_freestanding_function_as_string(): void {
		eval('function hook_test_function(array &$args): void { $args["called"] = true; }');
		insert_hook('test_hook', 'hook_test_function');
		$this->assertHookInvoked();
	}

	public function test_static_class_function_as_string(): void {
		insert_hook('test_hook', 'Zotlabs\Tests\Unit\CallHooksTest::static_test_hook');
		$this->assertHookInvoked();
	}

	public function test_static_class_function_as_array(): void {
		insert_hook('test_hook', ['Zotlabs\Tests\Unit\CallHooksTest', 'static_test_hook']);
		$this->assertHookInvoked();
	}

	public function test_static_class_function_as_serialized_array(): void {
		insert_hook('test_hook', serialize(['Zotlabs\Tests\Unit\CallHooksTest', 'static_test_hook']));
		$this->assertHookInvoked();
	}

	public function test_instance_function_as_array(): void {
		insert_hook('test_hook', [$this, 'instance_test_hook']);
		$this->assertHookInvoked();
	}


	public function assertHookInvoked(): void {
		$test_hook_args = ['called' => false];
		call_hooks('test_hook', $test_hook_args);

		$this->assertTrue($test_hook_args['called']);
	}

	public function instance_test_hook(array &$args): void {
		$args['called'] = true;
	}
	public static function static_test_hook(array &$args): void {
		$args['called'] = true;
	}
}


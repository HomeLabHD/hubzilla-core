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

use App;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\DataProvider;
use Zotlabs\Extend\Hook;

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

	#[DataProvider('hookProvider')]
	public function testOldInsertHookApi(mixed $hook): void {
		insert_hook('test_hook', $hook);
		$this->assertHookInvoked();
	}

	#[DataProvider('hookProvider')]
	public function testNewHookInsertApi(mixed $hook): void {
		Hook::insert('test_hook', $hook);
		$this->assertHookInvoked();
	}

	#[DataProvider('hookProvider')]
	public function testNewHookRegisterApi(mixed $hook): void {
		Hook::register('test_hook', __FILE__, $hook);

		load_hooks();
		$this->assertHookInvoked();

		Hook::unregister('test_hook', __FILE__, $hook);

		load_hooks();
		$this->assertNotHookInvoked();
	}

	//
	// Helper functions
	//

	private function invokeHook(): bool {
		$test_hook_args = ['called' => false];
		call_hooks('test_hook', $test_hook_args);

		return $test_hook_args['called'];
	}

	private function assertHookInvoked(): void {
		$this->assertTrue($this->invokeHook());
	}

	private function assertNotHookInvoked(): void {
		$this->assertFalse($this->invokeHook());
	}

	//
	// A static function to invoke via the hook
	//

	public static function static_test_hook(array &$args): void {
		$args['called'] = true;
	}

	//
	// Data provider for the hook tests
	//

	public static function hookProvider(): array {
		return [
			'hook is static class function as string' => [
				'Zotlabs\Tests\Unit\CallHooksTest::static_test_hook'
			],
			'hook is static class function as array' => [
				['Zotlabs\Tests\Unit\CallHooksTest', 'static_test_hook']
			],
		];
	}
}


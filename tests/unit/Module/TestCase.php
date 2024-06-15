<?php
/**
 * This file contains the base class for module test cases.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use Zotlabs\Tests\Unit\UnitTestCase;
use App;

/**
 * Base class for writing module tests.
 *
 * This test class adds a number of helper methods to the base from
 * the UnitTestCase class, useful when testing Modules (Controllers)
 * that handle incoming requests.
 */
class TestCase extends UnitTestCase {

	// Import PHPMock methods into this class
	use \phpmock\phpunit\PHPMock;

	/**
	 * Emulate a GET request.
	 *
	 * @param string $uri	The URI to request. Typically this will be the module
	 *						name, followed by any req args separated by slashes.
	 * @param array $query	Assciative array of query args, with the parameters
	 *						as keys.
	 */
	protected function get(string $uri, array $query = []): void {
		$_GET['q'] = $uri;

		if (!empty($query)) {
			$_GET = array_merge($_GET, $query);
		}

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['QUERY_STRING'] = "q={$uri}";
		// phpcs:disable Generic.PHP.DisallowRequestSuperglobal.Found
		$_REQUEST = $_GET;
		// phpcs::enable

		\App::init();
		\App::$page['content'] = '';

		$router = new \Zotlabs\Web\Router();
		$router->Dispatch();
	}

	/**
	 * Helper to simplify asserting contents in the rendered page.
	 *
	 * @param string $needle	The expected string to find.
	 */
	protected function assertPageContains(string $needle): void {
		$this->assertStringContainsString($needle, App::$page['content']);
	}

	/**
	 * Stub out the `killme` function.
	 *
	 * Useful for testing modules that call this function directly.
	 *
	 * Instead of calling exit, the stub will throw a `KillmeException`,
	 * that can be caught by the test code to regain control after request
	 * processing is terminated.
	 *
	 * **Example:**
	 *
	 *     public function test_something(): void {
	 *         $this->stub_killme();
	 *
	 *         try {
	 *             killme();
	 *         } catch (KillmeException $e) {
	 *             $this->assertSomething(...);
	 *         }
	 *     }
	 *
	 * It's also possible to use the builting PHPUnit expecations to verify
	 * that the function was called.
	 *
	 *     public function test_something(): void {
	 *         $this->stub_killme();
	 *         $this->expectException(KillmeException::class);
	 *
	 *         killme();
	 *     }
	 *
	 * This is useful if you only want to check that processing was terminated
	 * with the `killme()` function.
	 *
	 * @throws KillmeException
	 */
	protected function stub_killme(): void {
		$killme_stub = $this->getFunctionMock('Zotlabs\Module', 'killme');
		$killme_stub
			->expects($this->once())
			->willReturnCallback(
				function () {
					throw new KillmeException();
				}
			);
	}

	/**
	 * Stub out the `goaway` function.
	 *
	 * Useful for testing modules that calls this function directly.
	 *
	 * Instead of calling `killme()`, the stub will throw a RedirectException
	 * with the target URL as the exception message. This allows the test code
	 * to regain control after request processing is terminated.
	 *
	 * **Example:**
	 *
	 *     public function test_redirect(): void {
	 *         $this->stub_goaway();
	 *
	 *         try {
	 *             goaway('https://example.com/some_uri');
	 *         } catch (RedirectException $e) {
	 *             $this->assertEquals('https://example.com/some_uri', $e->getMessage());
	 *             $this->assertSomethingElse(...);
	 *         }
	 *     }
	 * It's also possible to use the builting PHPUnit expecations to verify
	 * that the function was called.
	 *
	 *     public function test_something(): void {
	 *         $this->stub_goaway();
	 *         $this->expectException(RedirectException::class);
	 *         $this->expectExceptionMessage('https://example.com/some_uri');
	 *
	 *         goaway('https://example.com/some_uri');
	 *     }
	 *
	 * This is useful if you only want to check that the request was redirected.
	 *
	 * @throws RedirectException
	 */
	protected function stub_goaway(): void {
		$goaway_stub = $this->getFunctionMock('Zotlabs\Module', 'goaway');
		$goaway_stub
			->expects($this->once())
			->willReturnCallback(
				function (string $uri) {
					throw new RedirectException($uri);
				}
			);
	}

	/**
	 * Shorthand function to expect a redirect to a given URL.
	 *
	 * **Example:**
	 *
	 *     public function test_redirect(): void {
	 *         $this->expectRedirectTo('https://example.com/some_uri');
	 *         goaway('https://example.com/some_uri');
	 *     }
	 */
	protected function expectRedirectTo(string $destination): void {
		$this->stub_goaway();
		$this->expectException(RedirectException::class);
		$this->expectExceptionMessage($destination);
	}
}

/**
 * Exception class for killme stub
 */
class KillmeException extends \Exception {}

/**
 * Exception class for goaway stub.
 *
 * Takes the goaway uri as an arg, and makes it available to the catch
 * site via the `getMessage()` method.
 */
class RedirectException extends \Exception {
	function __construct(string $uri) {
		parent::__construct($uri);
	}
}

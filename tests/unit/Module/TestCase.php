<?php

namespace Zotlabs\Tests\Unit\Module;

class TestCase extends \Zotlabs\Tests\Unit\UnitTestCase {

	// Import PHPMock methods into this class
	use \phpmock\phpunit\PHPMock;

	/**
	 * Emulate a GET request.
	 *
	 * @param string $uri	The URI to request. Typically this will be the module
	 *						name, followed by any req args separated by slashes.
	 *
	 * @param array $query	Assciative array of query args, with the parameters
	 *						as keys.
	 */
	protected function get(string $uri, array $query = []): void {
		$_GET['q'] = $uri;

		if (!empty($query)) {
			$_GET = array_merge($_GET, $query);
		}

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_REQUEST = $_GET;

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
		$this->assertStringContainsString($needle, \App::$page['content']);
	}

	/**
	 * Stub out the `killme` function.
	 *
	 * Usefule for modules that call this function directly.
	 *
	 * Instead of calling exit, the stub will throw a `KillmeException`,
	 * that can be caught by the test code to regain control after request
	 * processing is terminated.
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
}

/**
 * Exception class for killme stub
 */
class KillmeException extends \Exception {}

/**
 * Exception class for goaway stub.
 *
 * Takes the goaway uri as an arg, and makes it available as
 * the public `$uri` member variable.
 */
class RedirectException extends \Exception {
	function __construct(string $uri) {
		parent::__construct($uri);
	}
}

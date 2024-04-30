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
	 */
	protected function get(string $uri): void {
		$_GET['q'] = $uri;
		$_SERVER['REQUEST_METHOD'] = 'GET';

		\App::init();
		\App::$page['content'] = '';

		$router = new \Zotlabs\Web\Router();
		$router->Dispatch();
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

<?php
/*
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

use PHPUnit\Framework\Attributes\TestWith;

class ItemTest extends TestCase {

	#[TestWith(['application/x-zot+json'])]
	#[TestWith(['application/x-zot-activity+json'])]
	public function test_request_with_no_args_return_404(string $type): void {
		$this->expect_status(404, 'Not found');

		$_SERVER['HTTP_ACCEPT'] = $type;
		$this->get('item');
	}

	#[TestWith(['application/x-zot+json'])]
	#[TestWith(['application/x-zot-activity+json'])]
	public function test_request_with_non_exiting_idem_id(string $type): void {
		$this->expect_status(404, 'Not found');

		$_SERVER['HTTP_ACCEPT'] = $type;
		$this->get('item/non-existing-id');
	}

	/**
	 * Helper function to mock the `http_status_exit` function.
	 *
	 * The request will be terminated by throwing an exception, which
	 * will also terminate the test case. Iow. control will not return
	 * to the test case after the request has been made.
	 *
	 * @param int $status	The expected HTTP status code.
	 * @param string $description	The expected HTTP status description
	 */
	private function expect_status(int $status, string $description): void {
		$this->getFunctionMock('Zotlabs\Module', 'http_status_exit')
			->expects($this->once())
			->with($this->identicalTo($status), $this->identicalTo($description))
			->willReturnCallback(
				function () {
					throw new KillmeException();
				}
			);

		$this->expectException(KillmeException::class);

	}
}

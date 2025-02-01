<?php
/*
 * SPDX-FileCopyrightText: 2025 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Module;

class OwaTest extends TestCase
{
	public function testShouldReturnErrorIfNoAuthorizationHeader(): void
	{
		// Expect the call to return error
		$this->expectJsonResponse([
			'success' => false,
			'message' => 'Missing or invalid authorization header.'
		]);

		$this->get('owa');
	}

	public function testShouldReturnErrorIfWrongAuthorizationHeader(): void
	{
		// Expect the call to return error
		$this->expectJsonResponse([
			'success' => false,
			'message' => 'Missing or invalid authorization header.'
		]);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer kjkjhkjhkjh';
		$this->get('owa');
	}

	public function testShouldReturnErrorIfInvalidAuthorizationHeader(): void
	{
		// Expect the call to return error
		$this->expectJsonResponse(['success' => false]);

		$_SERVER['HTTP_AUTHORIZATION'] = 'Signature kjkjhkjhkjh';
		$this->get('owa');
	}

	/**
	 * Expect the request to be terminated and return a json response.
	 */
	private function expectJsonResponse(array $data): void
	{
		$this->getFunctionMock('Zotlabs\Module', 'json_return_and_die')
		    ->expects($this->once())
		    ->with(
				$this->identicalTo($data),
				$this->identicalTo('application/x-zot+json')
			)
			->willReturnCallback(
				function() {
					throw new KillmeException();
				}
			);

		$this->expectException(KillmeException::class);
	}
}

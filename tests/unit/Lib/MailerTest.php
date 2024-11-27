<?php
/*
 * Tests for the Zotlabs\LibM̀ailer class.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\Lib;

use App;
use phpmock\phpunit\PHPMock;
use Zotlabs\Lib\Mailer;
use Zotlabs\Tests\Unit\UnitTestCase;

class MailerTest extends UnitTestCase {

	use PHPMock;

	public function test_optional_params_replaced_by_defaults(): void {
		$hostname = App::get_hostname();
		$recipient = 'recipient@somesite.test';
		$subject = 'A test email';
		$body = <<<EOF
			Dear recipient,

			This is an test email message for you.

			Sincerely,
			Hubzilla
			EOF;

		//
		// Catch calls to the php mail function, and verify
		// that it is called with the args we're expecting
		//
		$this->getFunctionMock('Zotlabs\Lib', 'mail')
			->expects($this->once())
			->with(
				$this->identicalTo($recipient),
				$this->identicalTo($subject),
				$this->identicalTo($body),
				$this->identicalTo(<<<EOF
					From:  <Administrator@{$hostname}>
					Reply-To:  <noreply@{$hostname}>
					Content-Type: text/plain; charset=UTF-8
					EOF
				)
			)
			->willReturn(true);

		$mailer = new Mailer([
			'toEmail' => $recipient,
			'messageSubject' => $subject,
			'textVersion' => $body,
		]);

		$mailer->deliver();
	}
}

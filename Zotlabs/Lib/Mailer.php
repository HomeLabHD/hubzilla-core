<?php
/**
 * Mailer class for sending emails from Hubzilla.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use App;

/**
 * A class for sending emails.
 *
 * Based on the previous `z_mail` function, but adaped and made more
 * robust and usable as a class.
 */
class Mailer {

	public function __construct(private array $params = []) {
	}

	public function deliver(): bool {

		if(empty($this->params['fromEmail'])) {
			$this->params['fromEmail'] = Config::Get('system','from_email');
			if(empty($this->params['fromEmail'])) {
				$this->params['fromEmail'] = 'Administrator@' . App::get_hostname();
			}
		}

		if(empty($this->params['fromName'])) {
			$this->params['fromName'] = Config::Get('system','from_email_name');
			if(empty($this->params['fromName'])) {
				$this->params['fromName'] = System::get_site_name();
			}
		}

		if(empty($this->params['replyTo'])) {
			$this->params['replyTo'] = Config::Get('system','reply_address');
			if(empty($this->params['replyTo'])) {
				$this->params['replyTo'] = 'noreply@' . App::get_hostname();
			}
		}

		if (!isset($this->params['additionalMailHeader'])) {
			$this->params['additionalMailHeader'] = '';
		}

		$this->params['sent']   = false;
		$this->params['result'] = false;

		/**
		 * @hooks email_send
		 *   * \e params @see z_mail()
		 */
		call_hooks('email_send', $this->params);

		if($this->params['sent']) {
			logger('notification: z_mail returns ' . (($this->params['result']) ? 'success' : 'failure'), LOGGER_DEBUG);
			return $this->params['result'];
		}

		$fromName = email_header_encode(html_entity_decode($this->params['fromName'],ENT_QUOTES,'UTF-8'),'UTF-8');
		$messageSubject = email_header_encode(html_entity_decode($this->params['messageSubject'],ENT_QUOTES,'UTF-8'),'UTF-8');

		$messageHeader =
			$this->params['additionalMailHeader'] .
			"From: $fromName <{$this->params['fromEmail']}>" . PHP_EOL .
			"Reply-To: $fromName <{$this->params['replyTo']}>" . PHP_EOL .
			"Content-Type: text/plain; charset=UTF-8";

		// send the message
		$res = mail(
			$this->params['toEmail'],								// send to address
			$messageSubject,								// subject
			$this->params['textVersion'],
			$messageHeader									// message headers
		);
		logger('notification: z_mail returns ' . (($res) ? 'success' : 'failure'), LOGGER_DEBUG);
		return $res;
	}
}

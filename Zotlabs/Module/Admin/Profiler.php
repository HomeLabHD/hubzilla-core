<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Module\Admin;

use Zotlabs\Lib\SystemProfiler;
use Zotlabs\Web\Controller;

class Profiler extends Controller
{
	public function init(): void {

	}

	public function post(): void {
		$json_request = getBestSupportedMimeType(['application/json']) !== null;

		if (!$json_request) {
			http_status(400, 'Invalid request');
			killme();
		}

		$params = json_decode(file_get_contents('php://input'), true);

		if (empty($params['action'])) {
			notice(t('Invalid request'));
			return;
		}

		switch ($params['action']) {
			case 'enable_profiling':
				SystemProfiler::enable();
				info('Profiling enabled');
				json_return_and_die([
					'status' => 'success',
					'new_state' => [
						'action' => 'disable_profiling',
						'label' => t('Disable'),
					],
				]);

			case 'disable_profiling':
				SystemProfiler::disable();
				info('Profiling disabled');
				json_return_and_die([
					'status' => 'success',
					'new_state' => [
						'action' => 'enable_profiling',
						'label' => t('Enable'),
					],
				]);

			default:
				notice(t('Invalid action'));
				json_return_and_die([
					'status' => 'error',
					'message' => 'Invalid action',
				]);
		}
	}

	public function get(): string {
		return replace_macros(get_markup_template('admin_profiler.tpl'), [
			'this' => $this,
		]);
	}
}

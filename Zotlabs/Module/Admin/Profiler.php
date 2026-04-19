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

		if ($json_request) {
			$params = json_decode(file_get_contents('php://input'), true);
		} else {
			$params = $_POST;
		}

		if (empty($params['action'])) {
			notice(t('Invalid request'));
			return;
		}

		switch ($params['action']) {
			case 'enable_profiling':
				SystemProfiler::enable();
				info('Profiling enabled');
				break;

			case 'disable_profiling':
				SystemProfiler::disable();
				info('Profiling disabled');
				break;

			default:
				notice(t('Invalid action'));
		}

		if ($json_request) {
			json_return_and_die(['status' => 'success']);
		} else {
			goaway(z_root() . '/admin/profiler/');
		}
	}

	public function get(): string {
		return replace_macros(get_markup_template('admin_profiler.tpl'), [
			'this' => $this,
		]);
	}
}

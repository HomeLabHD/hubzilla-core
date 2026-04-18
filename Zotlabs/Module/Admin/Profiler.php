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
		if (empty($_POST['action'])) {
			notice(t('Invalid request'));
			return;
		}

		switch ($_POST['action']) {
			case 'enable_profiling':
				SystemProfiler::enable();
				info('Profiling enabled');
				break;

			case 'disable_profiling':
				SystemProfiler::disable();
				info('Profiling disabled');
				break;

			default:
				notice(t('Invalid request'));
		}
	}

	public function get(): string {
		return replace_macros(get_markup_template('admin_profiler.tpl'), [
			'this' => $this,
		]);
	}
}

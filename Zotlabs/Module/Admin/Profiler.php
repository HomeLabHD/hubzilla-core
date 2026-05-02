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
			$this->handleAjaxRequest();
		} else {
			$this->handleFormData();
		}
	}

	private function handleAjaxRequest(): void {
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

	private function handleFormData(): void {
		if (!check_form_Security_token('admin_profiler', 'security')) {
			notice(t('Invalid or expired request'));
			goaway(z_root() . '/admin/profiler');
		}

		if (!empty($_POST['filename'])) {
			SystemProfiler::setOutputFilename($_POST['filename']);
		}
	}

	public function get(): string {
		return replace_macros(get_markup_template('admin_profiler.tpl'), [
			'this' => $this,
			'security' => get_form_security_token('admin_profiler'),
			'title' => t('System profiler settings'),
			'submit' => t('Save'),
			'option_filename' => [
				'filename',
				t("Output pathname"),
				SystemProfiler::outputFilename(),
				translate_projectname(
					t('The path and filename where to save the profiling data. Relative to the $Projectname root directory.')
				),
			],
		]);
	}
}

<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Lib;

use Xhgui\Profiler\Profiler;
use Xhgui\Profiler\ProfilingFlags;

class SystemProfiler
{
	public static function start(): void {
		$config = [
			'profiler.flags' => [
				ProfilingFlags::CPU,
				ProfilingFlags::MEMORY,
				ProfilingFlags::NO_BUILTINS,
				ProfilingFlags::NO_SPANS,
			],
			'save.handler' => Profiler::SAVER_FILE,
			'save.handler.file' => [
				'filename' => self::outputFilename(),
			],
			'profiler.enable' => fn () => self::isEnabled(),
		];

		try {
			$p = new Profiler($config);
			$p->start();
		} catch (\Exception $e) {
			logger('Profiler not enabled, exception: ' . $e->getMessage(), LOGGER_DEBUG);
		}
	}

	public static function enable(): void {
		Config::Set('system', 'profiling_enabled', true);
	}

	public static function disable(): void {
		Config::Set('system', 'profiling_enabled', false);
	}

	public static function isEnabled(): bool {
		return !!Config::Get('system', 'profiling_enabled', false);
	}

	public static function outputFilename(): string {
		return Config::Get('system', 'profiling_output_path', 'xhgui.data.jsonl');
	}

	public static function setOutputFilename(string $path): void {
		Config::Set('system', 'profiling_output_path', $path);
	}
}

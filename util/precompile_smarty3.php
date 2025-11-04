<?php
/* Utility script to compile all smarty templates
 *
 * SPDX-FileCopyrightText: 2013, 2025 The Hubzilla Community
 * SPDX-FileContributor: fabrixxm <fabrix.xm@gmail.com>
 * SPDX-FileContributor: Mario <mario@mariovavti.com>
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 *
 * SPDX-License-Identifier: MIT
 */

require_once dirname(__DIR__) . '/boot.php';

$folders = array_merge(array('view/tpl/'), glob('view/theme/*/tpl/*', GLOB_ONLYDIR));

$s = new \Smarty\Smarty();

$s->setTemplateDir($folders);

$s->setCompileDir(TEMPLATE_BUILD_PATH . '/compiled/');
$s->setConfigDir(TEMPLATE_BUILD_PATH . '/config/');
$s->setCacheDir(TEMPLATE_BUILD_PATH . '/cache/');

$s->setLeftDelimiter('{{');
$s->setRightDelimiter('}}');

// Capture the output...
ob_start();
$s->compileAllTemplates('.tpl', true);
$buf = ob_get_flush();

// ...so that we can check if there was any errors
if (strpos($buf, '------>Error: ') !== false) {
	// Signal to the shell if there was
	exit(-1);
}

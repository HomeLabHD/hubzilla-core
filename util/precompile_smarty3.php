<?php

/**
* @package util
*/

require_once dirname(__DIR__) . '/boot.php';

$folders = array_merge(array('view/tpl/'), glob('view/theme/*/tpl/*', GLOB_ONLYDIR));

$s = new \Smarty\Smarty();

$s->setTemplateDir($folders);

$s->setCompileDir(TEMPLATE_BUILD_PATH . '/compiled/');
$s->setConfigDir(TEMPLATE_BUILD_PATH . '/config/');
$s->setCacheDir(TEMPLATE_BUILD_PATH . '/cache/');

$s->left_delimiter = '{{';
$s->right_delimiter = '}}';

$s->compileAllTemplates('.tpl', true);

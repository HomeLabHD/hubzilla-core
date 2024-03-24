<?php /** @file */

require_once('boot.php');

use Zotlabs\Lib\Config;

// Everything we need to boot standalone 'background' processes

function cli_startup() {

	sys_boot();
	App::set_baseurl(Config::Get('system','baseurl'));

}

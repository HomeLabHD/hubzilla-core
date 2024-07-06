<?php

namespace Zotlabs\Update;

use Zotlabs\Lib\Config;

class _1103 {
function run() {
	$x = curl_version();
	if(stristr($x['ssl_version'],'openssl'))
		Config::Set('system','curl_ssl_ciphers','ALL:!eNULL');
	return UPDATE_SUCCESS;
}


}

<?php

namespace Zotlabs\Module;

use Zotlabs\Lib\Zotfinger;
use Zotlabs\Web\HTTPSig;

class Zot_probe extends \Zotlabs\Web\Controller {

	function get() {

		if (!local_channel()) {
			return;
		}

		$addr = $_GET['addr'] ?? '';

		$o = '<h3>Zot6 Probe Diagnostic</h3>';

		$o .= '<form action="zot_probe" method="get">';
		$o .= 'Lookup URI: <input type="text" style="width: 250px;" name="addr" value="' . $addr .'" /><br>';
		$o .= '<input type="checkbox" name="sign" /> Sign request <br>';
		$o .= '<input type="submit" name="submit" value="Submit" /></form>';

		$o .= '<br /><br />';

		if($addr) {
			$channel = null;
			if ($_GET['sign']) {
				$channel = get_sys_channel();
			}

			$x = Zotfinger::exec($addr, $channel);
			$o .= '<pre>' . htmlspecialchars(print_array($x)) . '</pre>';
		}
		return $o;
	}

}

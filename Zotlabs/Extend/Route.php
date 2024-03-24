<?php

namespace Zotlabs\Extend;

use Zotlabs\Lib\Config;

class Route {

	static function register($file,$modname) {
		$rt = self::get();
		$rt[] = [ $file, $modname ];
		self::set($rt);
	}

	static function unregister($file,$modname) {
		$rt = self::get();
		if($rt) {
			$n = [];
			foreach($rt as $r) {
				if($r[0] !== $file && $r[1] !== $modname) {
					$n[] = $r;
				}
			}
			self::set($n);
		}
	}

	static function unregister_by_file($file) {
		$rt = self::get();
		if($rt) {
			$n = [];
			foreach($rt as $r) {
				if($r[0] !== $file) {
					$n[] = $r;
				}
			}
			self::set($n);
		}
	}

	static function get() {
		return Config::Get('system','routes',[]);
	}

	static function set($r) {
		return Config::Set('system','routes',$r);
	}
}


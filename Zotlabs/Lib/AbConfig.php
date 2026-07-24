<?php

namespace Zotlabs\Lib;

use DBA;
use PDO;
use PDOStatement;

class AbConfig {

	private static $seletctStmt;
	private static $insertStmt;
	private static $updateStmt;

	public static function Load($chan,$xhash,$family = '') {
		$where = '';

		if($family) {
			$where = sprintf(" and cat = '%s' ",dbesc($family));
		}

		$r = q("select * from abconfig where chan = %d and xchan = '%s' $where",
			intval($chan),
			dbesc($xhash)
		);

		return $r;
	}


	public static function Get($chan, $xhash, $family, $key, $default = false) {
		$dbargs = [':chan' => $chan, ':xchan' => $xhash, ':cat' => $family, ':k' => $key];

		if (!self::$seletctStmt instanceof PDOStatement) {
			self::$seletctStmt = self::prepareSelect();
		}

		self::$seletctStmt->execute($dbargs);
		$r = self::$seletctStmt->fetch(PDO::FETCH_ASSOC);

		if($r) {
			return ((preg_match('|^a:[0-9]+:{.*}$|s', $r['v'])) ? unserialize($r['v']) : $r['v']);
		}

		return $default;
	}


	public static function Set($chan,$xhash,$family,$key,$value) {
		$dbvalue = ((is_array($value))  ? serialize($value) : $value);
		$dbvalue = ((is_bool($dbvalue)) ? intval($dbvalue)  : $dbvalue);
		$dbargs = [':chan' => $chan, ':xchan' => $xhash, ':cat' => $family, ':k' => $key, ':v' => $dbvalue];

		$r = null;

		if(self::Get($chan, $xhash, $family, $key) === false) {
			if (!self::$insertStmt instanceof PDOStatement) {
				self::$insertStmt = self::prepareInsert();
			}

			$r = self::$insertStmt->execute($dbargs);
		}
		else {
			if (!self::$updateStmt instanceof PDOStatement) {
				self::$updateStmt = self::prepareUpdate();
			}

			$r = self::$updateStmt->execute($dbargs);
		}

		if ($r) {
			return $value;
		}

		return false;
	}


	public static function Delete($chan,$xhash,$family,$key) {
		$r = q("delete from abconfig where chan = %d and xchan = '%s' and cat = '%s' and k = '%s' ",
			intval($chan),
			dbesc($xhash),
			dbesc($family),
			dbesc($key)
		);

		return $r;
	}

	private static function prepareSelect(): PDOStatement {
		return DBA::$dba->db->prepare("select * from abconfig where chan = :chan and xchan = :xchan and cat = :cat and k = :k limit 1");
	}

	private static function prepareInsert(): PDOStatement {
		return DBA::$dba->db->prepare("insert into abconfig (chan, xchan, cat, k, v) values (:chan, :xchan, :cat, :k, :v)");
	}

	private static function prepareUpdate(): PDOStatement {
		return DBA::$dba->db->prepare("update abconfig set v = :v where chan = :chan and xchan = :xchan and cat = :cat and k = :k");
	}
}

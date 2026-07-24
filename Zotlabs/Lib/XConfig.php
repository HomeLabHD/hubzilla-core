<?php

namespace Zotlabs\Lib;

use App;
use DBA;
use PDO;
use PDOStatement;

/**
 * @brief Class for handling observer's config.
 *
 * <b>XConfig</b> is comparable to <i>PConfig</i>, except that it uses <i>xchan</i>
 * (an observer hash) as an identifier.
 *
 * <b>XConfig</b> is used for observer specific configurations and takes a
 * <i>xchan</i> as identifier.
 * The storage is of size MEDIUMTEXT.
 *
 * @code{.php}$var = Zotlabs\Lib\XConfig::Get('xchan', 'category', 'key');
 * // with default value for non existent key
 * $var = Zotlabs\Lib\XConfig::Get('xchan', 'category', 'unsetkey', 'defaultvalue');@endcode
 *
 * The old (deprecated?) way to access a XConfig value is:
 * @code{.php}$observer = App::get_observer_hash();
 * if ($observer) {
 *     $var = get_xconfig($observer, 'category', 'key');
 * }@endcode
 */
class XConfig {

	private static $seletctStmt;
	private static $insertStmt;
	private static $updateStmt;

	/**
	 * @brief Loads a full xchan's configuration into a cached storage.
	 *
	 * All configuration values of the given observer hash are stored in global
	 * cache which is available under the global variable App::$config[$xchan].
	 *
	 * @param string $xchan
	 *  The observer's hash
	 * @return void|false Returns false if xchan is not set
	 */
	static public function Load($xchan) {

		if (!$xchan) {
			return false;
		}

		if (!self::$seletctStmt instanceof PDOStatement) {
			self::$seletctStmt = self::prepareSelect();
		}

		self::$seletctStmt->execute([':xchan' => $xchan]);
		$r = self::$seletctStmt->fetchAll(PDO::FETCH_ASSOC);

		if (!array_key_exists($xchan, App::$config)) {
			App::$config[$xchan] = array();
		}

		if($r) {
			foreach($r as $rr) {
				$k = $rr['k'];
				$c = $rr['cat'];
				if(! array_key_exists($c, App::$config[$xchan])) {
					App::$config[$xchan][$c] = array();
					App::$config[$xchan][$c]['config_loaded'] = true;
				}
				App::$config[$xchan][$c][$k] = $rr['v'];
			}
		}
	}

	/**
	 * @brief Get a particular observer's config variable given the category
	 * name ($family) and a key.
	 *
	 * Get a particular observer's config value from the given category ($family)
	 * and the $key from a cached storage in App::$config[$xchan].
	 *
	 * Returns false if not set.
	 *
	 * @param string $xchan
	 *  The observer's hash
	 * @param string $family
	 *  The category of the configuration value
	 * @param string $key
	 *  The configuration key to query
	 * @param boolean $default (optional) default false
	 * @return mixed Stored $value or false if it does not exist
	 */
	static public function Get($xchan, $family, $key, $default = false) {
		if (!$xchan) {
			return $default;
		}

		if (!array_key_exists($xchan, App::$config)) {
			self::Load($xchan);
		}

		if (!array_key_exists($family, App::$config[$xchan]) || !array_key_exists($key, App::$config[$xchan][$family])) {
			return $default;
		}

		return ((!is_array(App::$config[$xchan][$family][$key]) && preg_match('|^a:[0-9]+:{.*}$|s', App::$config[$xchan][$family][$key]))
			? unserialize(App::$config[$xchan][$family][$key])
			: App::$config[$xchan][$family][$key]
		);
	}

	/**
	 * @brief Sets a configuration value for an observer.
	 *
	 * Stores a config value ($value) in the category ($family) under the key ($key)
	 * for the observer's $xchan hash.
	 *
	 * @param string $xchan
	 *  The observer's hash
	 * @param string $family
	 *  The category of the configuration value
	 * @param string $key
	 *  The configuration key to set
	 * @param string $value
	 *  The value to store
	 * @return mixed Stored $value or false
	 */
	static public function Set($xchan, $family, $key, $value) {
		// manage array value
		$dbvalue = ((is_array($value))  ? serialize($value) : $value);
		$dbvalue = ((is_bool($dbvalue)) ? intval($dbvalue)  : $dbvalue);
		$dbargs = [':xchan' => $xchan, ':cat' => $family, ':k' => $key, ':v' => $dbvalue];

		$ret = null;

		if(self::Get($xchan, $family, $key) === false) {
			if (!array_key_exists($xchan, App::$config)) {
				App::$config[$xchan] = array();
			}

			if (!array_key_exists($family, App::$config[$xchan])) {
				App::$config[$xchan][$family] = array();
			}

			if (!self::$insertStmt instanceof PDOStatement) {
				self::$insertStmt = self::prepareInsert();
			}

			$ret = self::$insertStmt->execute($dbargs);
		}
		else {
			if (!self::$updateStmt instanceof PDOStatement) {
				self::$updateStmt = self::prepareUpdate();
			}

			$ret = self::$updateStmt->execute($dbargs);
		}

		App::$config[$xchan][$family][$key] = $value;

		if ($ret) {
			return $value;
		}

		return false;
	}

	/**
	 * @brief Deletes the given key from the observer's config.
	 *
	 * Removes the configured value from the stored cache in App::$config[$xchan]
	 * and removes it from the database.
	 *
	 * @param string $xchan
	 *  The observer's hash
	 * @param string $family
	 *  The category of the configuration value
	 * @param string $key
	 *  The configuration key to delete
	 * @return mixed
	 */
	static public function Delete($xchan, $family, $key) {
		if (isset(App::$config[$xchan][$family][$key])) {
			unset(App::$config[$xchan][$family][$key]);
		}

		$ret = q("DELETE FROM xconfig WHERE xchan = '%s' AND cat = '%s' AND k = '%s'",
			dbesc($xchan),
			dbesc($family),
			dbesc($key)
		);

		return $ret;
	}

	private static function prepareSelect(): PDOStatement {
		return DBA::$dba->db->prepare("SELECT * FROM xconfig WHERE xchan = :xchan");
	}

	private static function prepareInsert(): PDOStatement {
		return DBA::$dba->db->prepare("INSERT INTO xconfig (xchan, cat, k, v) VALUES (:xchan, :cat, :k, :v)");
	}

	private static function prepareUpdate(): PDOStatement {
		return DBA::$dba->db->prepare("UPDATE xconfig SET v = :v WHERE xchan = :xchan AND cat = :cat AND k = :k");
	}
}

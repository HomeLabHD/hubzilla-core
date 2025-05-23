<?php

require_once 'include/dba/dba_driver.php';

/**
 * @brief PDO based database driver.
 *
 */
class dba_pdo extends dba_driver {

	public $driver_dbtype = null;

	private string $server_version = '';

	/**
	 * {@inheritDoc}
	 * @see dba_driver::connect()
	 */
	function connect($server, $scheme, $port, $user, $pass, $db, $db_charset) {

		$this->driver_dbtype = $scheme;

		if(strpbrk($server,':;')) {
			$dsn = $this->driver_dbtype . ':unix_socket=' . trim($server, ':;');
		}
		else {
			$dsn = $this->driver_dbtype . ':host=' . $server . (intval($port) ? ';port=' . $port : '');
		}

		$dsn .= ';dbname=' . $db;

		if ($this->driver_dbtype === 'mysql') {
			$dsn .= ';charset=' . $db_charset;
		}
		else {
			$dsn .= ";options='--client_encoding=" . $db_charset . "'";
		}

		try {
			$this->db = new PDO($dsn,$user,$pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$this->server_version = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
		}
		catch(PDOException $e) {
			if(file_exists('dbfail.out')) {
				file_put_contents('dbfail.out', datetime_convert() . "\nConnect: " . $e->getMessage() . "\n", FILE_APPEND);
			}

			return false;
		}

		if($this->driver_dbtype === 'pgsql')
			$this->q("SET standard_conforming_strings = 'off'; SET backslash_quote = 'on';");

		$this->connected = true;

		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see dba_driver::q()
	 *
	 * @return bool|array|PDOStatement
	 *   - \b false if not connected or PDOException occured on query
	 *   - \b array with results on a SELECT query
	 *   - \b PDOStatement on a non SELECT SQL query
	 */
	function q($sql) {
		if((! $this->db) || (! $this->connected))
			return false;

		if($this->driver_dbtype === 'pgsql') {
			if(substr(rtrim($sql),-1,1) !== ';') {
				$sql .= ';';
			}
		}

		$result = false;
		$this->error = '';
		$select = stripos($sql, 'select') === 0 || stripos($sql, 'with') === 0 || stripos($sql, 'returning ') > 0;

		try {
			$result = $this->db->query($sql, PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {

			$this->error = $e->getMessage();
			if($this->error) {
				db_logger('dba_pdo: ERROR: ' . printable($sql) . "\n" . $this->error, LOGGER_NORMAL, LOG_ERR);
				if(file_exists('dbfail.out')) {
					file_put_contents('dbfail.out', datetime_convert() . "\n" . printable($sql) . "\n" . $this->error . "\n", FILE_APPEND);
				}
			}
		}

		if(!($select)) {
			if($this->debug) {
				db_logger('dba_pdo: DEBUG: ' . printable($sql) . ' returns ' . (($result) ? 'true' : 'false'), LOGGER_NORMAL,(($result) ? LOG_INFO : LOG_ERR));
			}
			return $result;
		}

		$r = array();
		if($result) {
			foreach($result as $x) {
				$r[] = $x;
			}
		}

		if($this->debug) {
			db_logger('dba_pdo: DEBUG: ' . printable($sql) . ' returned ' . count($r) . ' results.', LOGGER_NORMAL, LOG_INFO);
			if(intval($this->debug) > 1) {
				db_logger('dba_pdo: ' . printable(print_r($r,true)), LOGGER_NORMAL, LOG_INFO);
			}
		}

		return (($this->error) ? false : $r);
	}

	/**
	 * Insert a row into a table.
	 *
	 * The `$data` argument is an array of key/value pairs of the columns to
	 * insert, where the key is the column name. Values are automatically
	 * escaped if needed, and should be provided unescaped to this function.
	 *
	 * @note it is the callers responsibility to ensure that only valid
	 * column names are passed as keys in the array.
	 *
	 * The inserted row will be returned.
	 *
	 * @param string $table		The table to insert the row into.
	 * @param array $data		The data to insert as an array of column name => value pairs.
	 * @param string $idcol		The column name for the primary key of the table. We need to
	 *                          specify this since we don't have a consistent naming of primary
	 *                          id for tables.
	 *
	 * @return array|bool	The complete record as read back from the database, or false if we
	 *                      could not fetch it.
	 */
	public function insert(string $table, array $data, string $idcol): array|bool {
		$keys = array_keys($data);
		$values = array_map(
			fn ($v) => is_numeric($v) ? $v : "'" . dbesc($v) . "'",
			array_values($data)
		);

		$query = "INSERT INTO {$table} ("
			. implode(', ', $keys) . ') VALUES ('
			. implode(', ', $values) . ')';

		// MySQL is the only supported DB that don't support the returning
		// clause. Since the driver type is 'mysql' also for MariaDB, we need
		// to check the actual server version to be sure we only exclude actual
		// MySQL systems.
		if ($this->driver_dbtype !== 'mysql' || stripos($this->server_version, 'mariadb') !== false) {
			$query .= ' RETURNING *';
		}

		$res = $this->q($query);

		if (is_a($res, PDOStatement::class)) {
			//
			// Calling PDO::lastInsertId should be safe here.
			// The last inserted id is kept for each connection, so we're not risking
			// a race condition wrt inserts by other requests that happen simultaneously.
			//
			$id = $this->db->lastInsertId($table);

			$res = $this->q("SELECT * FROM {$table} WHERE {$idcol} = {$id}");

			if (is_a($res, PDOStatement::class)) {
				db_logger('dba_pdo: PDOStatement returned, did not expect that.');
				return false;
			}
		}

		if (is_array($res)) {
			// Since we should never have more than one result, unwrap the array
			// so we only have the resulting row.
			$res = $res[0];
		}

		return $res;
	}

	/**
	 * Update an existing row in a table.
	 *
	 * The `$data` argument is an array of key/value pairs of the columns to
	 * update, where the key is the column name. Values are automatically
	 * escaped if needed, and should be provided unescaped to this function.
	 *
	 * @note it is the callers responsibility to ensure that only valid
	 * column names are passed as keys in the array.
	 *
	 * The row to be updated is identified by `$idcol` and `$idval` as the
	 * column name and value respectively. This should normally be the unique
	 * id column of the table, but can in theory be any column with a unique
	 * value that identifies a specific row.
	 *
	 * @param string $table		The table to update.
	 * @param array $data		The columns to update as key => value pairs.
	 * @param string $idcol		The name of the id column to check $idval against.
	 * @param mixed $idval		The id of the row to update.
	 *
	 * @return bool		True if the update succeeded, false otherwise.
	 */
	public function update(string $table, array $data, string $idcol, mixed $idval): bool {
		$set_statements = [];

		foreach ($data as $k => $v) {
			$set_statements[] = "set {$k}=" . (is_numeric($v) ? $v : "'" . dbesc($v) . "'");
		}

		$query = "UPDATE {$table} "
			. implode(', ', $set_statements)
			. " WHERE {$idcol} = {$idval}";

		$res = $this->q($query);

		return is_a($res, PDOStatement::class);
	}

	function escape($str) {
		if($this->db && $this->connected) {
			return substr(substr(@$this->db->quote($str),1),0,-1);
		}
	}

	function close() {
		if($this->db)
			$this->db = null;

		$this->connected = false;
	}

	function concat($fld,$sep) {
		if($this->driver_dbtype === 'pgsql') {
			return 'string_agg(' . $fld . ',\'' . $sep . '\')';
		}
		else {
			return 'GROUP_CONCAT(DISTINCT ' . $fld . ' SEPARATOR \'' . $sep . '\')';
		}
	}

	function use_index($str) {
		if($this->driver_dbtype === 'pgsql') {
			return '';
		}
		else {
			return 'USE INDEX( ' . $str . ')';
		}
	}

	function str_to_date($str) {
		if($this->driver_dbtype === 'pgsql') {
			return "TO_TIMESTAMP($str, 'YYYY-MM-DD HH24:MI:SS')";
		}
		else {
			return "STR_TO_DATE($str, '%Y-%m-%d %H:%i:%s')";
		}
	}

	function quote_interval($txt) {
		if($this->driver_dbtype === 'pgsql') {
			return "'$txt'";
		}
		else {
			return $txt;
		}
	}

	// These two functions assume that postgres standard_conforming_strings is set to off;
	// which we perform during DB open.

	function escapebin($str) {
		if($this->driver_dbtype === 'pgsql') {
			return "\\\\x" . bin2hex($str);
		}
		else {
			return $this->escape($str);
		}
	}

	function unescapebin($str) {
		if($this->driver_dbtype === 'pgsql') {
			if(gettype($str) === 'resource') {
				$str = stream_get_contents($str);
			}
		if(substr($str,0,2) === '\\x') {
				$str = hex2bin(substr($str,2));
			}
		}
		return $str;
	}

	function getdriver() {
		return 'pdo';
	}

}

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
	 *
	 * @see dba_driver::connect()
	 */
	function connect(): bool {

		$this->driver_dbtype = $this->scheme;

                $dbhost = $this->server;
                
                // We no longer require unix socket paths in the $db_host configuration in .htconfig.php
                // to be prefixed by a colon (':'). This block handles legacy configuration and could
                // eventually be removed if we don't expect old legacy config to still exist.
                // For now we try not to break old configurations.
                if(str_starts_with($dbhost, ':')) {
                        db_logger('dba_pdo: WARN: the unix socket path "' . $dbhost . '" in .htconfig.php should not be prefixed with a colon.', LOGGER_NORMAL, LOG_WARNING);
                        $dbhost = trim($dbhost, ':');
                }

                if(str_contains($dbhost, '/') and file_exists($dbhost)) {
                        db_logger('dba_pdo: DEBUG: the db_host "' . $dbhost . '" looks like a unix socket and the file exists.', LOGGER_NORMAL, LOG_DEBUG);
                        $dsn = $this->driver_dbtype . ':unix_socket=' . $dbhost;
                }
                else {
                        db_logger('dba_pdo: DEBUG: the db_host "' . $dbhost . '" is not a path to an existing file. Assuming IP or hostname.', LOGGER_NORMAL, LOG_DEBUG);
                        $dsn = $this->driver_dbtype
                                . ':host='
                                . $dbhost
                                . (intval($this->port) ? ';port=' . $this->port : '');
                }

		$dsn .= ';dbname=' . $this->dbname;

		if ($this->driver_dbtype === 'mysql') {
			$dsn .= ';charset=' . $this->db_charset;
		}
		else {
			$dsn .= ";options='--client_encoding=" . $this->db_charset . "'";
		}

		try {
			$this->db = new PDO($dsn, $this->user, $this->pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
		$select = stripos($sql, 'select') === 0 || stripos($sql, 'with') === 0;

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

	function p(string $sql): PDOStatement | false {
		if (!$this->db || !$this->connected) {
			return false;
		}

		if ($this->driver_dbtype === 'pgsql') {
			if(substr(rtrim($sql),-1,1) !== ';') {
				$sql .= ';';
			}
		}

		$result = false;

		try {
			$stmt = $this->db->prepare($sql);
			hz_syslog(print_r($stmt, true));
		}
		catch(PDOException $e) {
			$this->error = $e->getMessage();
			if ($this->error) {
				db_logger('dba_pdo: ERROR: ' . printable($sql) . "\n" . $this->error, LOGGER_NORMAL, LOG_ERR);
				if(file_exists('dbfail.out')) {
					file_put_contents('dbfail.out', datetime_convert() . "\n" . printable($sql) . "\n" . $this->error . "\n", FILE_APPEND);
				}
			}
		}

		return (($this->error) ? false : $stmt);
	}

	function e(PDOStatement $stmt, array $args): array | false {
		if (!$this->db || !$this->connected) {
			return false;
		}

		$result = false;

		try {
			$stmt->execute($args);
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			$this->error = $e->getMessage();
			if ($this->error) {
				db_logger('dba_pdo: ERROR: ' . printable($stmt->queryString) . "\n" . $this->error, LOGGER_NORMAL, LOG_ERR);
				if(file_exists('dbfail.out')) {
					file_put_contents('dbfail.out', datetime_convert() . "\n" . printable($stmt->queryString) . "\n" . $this->error . "\n", FILE_APPEND);
				}
			}
		}

		if($this->debug) {
			db_logger('dba_pdo: DEBUG: ' . printable($stmt->queryString) . ' returned ' . count($result) . ' results.', LOGGER_NORMAL, LOG_INFO);
			if(intval($this->debug) > 1) {
				db_logger('dba_pdo: ' . printable(print_r($result,true)), LOGGER_NORMAL, LOG_INFO);
			}
		}

		return (($this->error) ? false : $result);
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
	 *
	 * @return array|bool	The complete record as read back from the database, or false if we
	 *                      could not fetch it.
	 */
	public function insert(string $table, array $data): array|bool {
		$keys = array_keys($data);
		$values = array_map(
			fn ($v) => is_numeric($v) ? $v : "'" . dbesc($v) . "'",
			array_values($data)
		);

		$query = "INSERT INTO {$table} ("
			. implode(', ', $keys) . ') VALUES ('
			. implode(', ', $values) . ')';

		if ($this->is_mysql()) {
			$this->db->exec($query);

			// MySQL don't support INSERT ... RETURNING, so we have to fetch
			// the inserted data manually.
			//
			// Calling PDO::lastInsertId should be safe here.
			//
			// The last inserted id is kept for each connection, so we're not
			// risking a race condition wrt inserts by other requests that
			// happen simultaneously.
			//
			$id = $this->db->lastInsertId($table);
			$id_col = $this->get_id_col($table);

			// LAST_INSERT_ID() will return 0 or null if the id column was
			// specified in the INSERT statement. Use the specified id to
			// reload from the db.
			if (intval($id) == 0) {
				$id = $data[$id_col];
			}

			$st = $this->db->prepare("SELECT * FROM {$table} WHERE {$id_col} = ?");
			$st->execute([$id]);
		} else {
			// Postgres and MariaDB support retruning the data immediately, so
			// add the RETURNING clause to the query
			$query .= ' RETURNING *';

			$st = $this->db->query($query);
		}

		return $st->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Return the name of the column for the primary key for a given table.
	 *
	 * @param string $table		The table whose primary key column we want.
	 *
	 * @return string	The name of the column for the primary key.
	 */
	private function get_id_col(string $table): string {
		$id_col = '';

		$st = $this->db->query("SHOW INDEX from {$table} WHERE key_name = 'PRIMARY'");
		if ($st !== false) {
			$res = $st->fetch(PDO::FETCH_ASSOC);
			$id_col = $res['Column_name'];
		}

		return $id_col;
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

	/**
	 * Return true if this we're running on a MySQL server.
	 *
	 * Note, this will return `false` on MariaDB.
	 *
	 * @return	true if the database is a MySQL server instance.
	 */
	public function is_mysql(): bool {
		return $this->driver_dbtype === 'mysql' &&
			stripos($this->server_version, 'mariadb') === false;
	}

	/**
	 * Return true if we're running on a PostgreSQL server.
	 *
	 * @return	true if the database is a PostgreSQL server instance.
	 */
	public function is_postgres(): bool {
		return $this->driver_dbtype === 'pgsql';
	}
}

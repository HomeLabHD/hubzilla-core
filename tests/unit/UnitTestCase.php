<?php
/* Copyright (c) 2016 Hubzilla
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Zotlabs\Tests\Unit;

use DBA;
use PHPUnit\Framework\Attributes\{Before, After};
use PHPUnit\Framework\TestCase;

/*
 * Make sure global constants and the global App object is available to the
 * tests.
 */
require_once __DIR__ . '/../../boot.php';
require_once 'include/dba/dba_driver.php' ;
require_once 'include/dba/dba_transaction.php';

/**
 * Base class for our Unit Tests.
 *
 * Base class for Hubzilla unit/integration tests. This extends the base
 * TestCase class from PHPUnit by connecting to a test database, and making the
 * database connection available to the code under test via the normal Hubzilla
 * mechanisms, i.e the \DBA::$dba global variable.
 *
 * It also automatically loads database fixtures from yaml files in the
 * tests/unit/includes/dba/_files directory. And wraps each test run in it's
 * own database transaction.
 */
class UnitTestCase extends TestCase {
	protected array $fixtures = array();
	protected ?\DbaTransaction $db_transaction = null;

	/**
	 * Connect to the test db, load fixtures and global config.
	 *
	 * This function is executed before every test.
	 *
	 * The transaction is rolled back in the `cleanup_test_db()` function
	 * that's executed after every test.
	 */
	#[Before]
	protected function setup_test_db(): void {
		if (! \DBA::$dba) {
			$this->connect_to_test_db();
		}

		// The $transactuion variable is needed to hold the transaction until the
		// function returns.
		$this->db_transaction = new \DbaTransaction(\DBA::$dba);

		$this->loadFixtures();

		// Make sure app config is reset and loaded from fixtures
		\App::$config = array();
		\Zotlabs\Lib\Config::Load('system');
	}

	/**
	 * Initialize the global App properties.
	 */
	#[Before]
	protected function init_app(): void {
		\App::set_hostname('hubzilla.test');
	}

	/**
	 * Roll back test database to it's original state, cleaning up
	 * any changes from the test.
	 *
	 * This function is executes after evert tests.
	 */
	#[After]
	protected function cleanup_test_db(): void {
		// Setting the transaction to `null`, runs the destructor
		// which rolls backk the transacton.
		$this->db_transaction = null;
	}

	/**
	 * Connect to the test database,
	 *
	 * By default it will connect to a MySQL database with the following settings:
	 *
	 *   - HZ_TEST_DB_HOST: db
	 *   - HZ_TEST_DB_PORT: default
	 *   - HZ_TEST_DB_USER: test_user
	 *   - HZ_TEST_DB_PASS: hubzilla
	 *   - HZ_TEST_DB_DATABASE: hubzilla_test_db
	 *   - HZ_TEST_DB_TYPE: mysql (can also be "postgres")
	 *   - HZ_TEST_DB_CHARSET: UTF8
	 *
	 * All of these settings can be overridden by the test runner by setting ENV vars
	 * named as above with the values you want to override.
	 */
	protected function connect_to_test_db() : void {
		if ( !\DBA::$dba ) {
			\DBA::dba_factory(
				getenv('HZ_TEST_DB_HOST') ?: 'localhost',

				// Use default port for db type if none specified
				getenv('HZ_TEST_DB_PORT'),
				getenv('HZ_TEST_DB_USER') ?: 'test_user',
				getenv('HZ_TEST_DB_PASS') ?: 'hubzilla',
				getenv('HZ_TEST_DB_DATABASE') ?: 'hubzilla_test_db',
				self::dbtype(getenv('HZ_TEST_DB_TYPE')),
				getenv('HZ_TEST_DB_CHARSET') ?: 'UTF8',
				false);

			if ( !\DBA::$dba->connected ) {
				$msg = "Unable to connect to db! ";
				if(file_exists('dbfail.out')) {
					$msg .= file_get_contents('dbfail.out');
				}

				throw new \Exception($msg);
			}

			\DBA::$dba->dbg(true);
		}
	}

	/**
	 * Return the database type from a string.
	 *
	 * @param string $type	The database type, can be either mysql or postgres.
	 *
	 * @return The database type constant matching the passed in type, or DBTYPE_MYSQL
	 *			if $type is empty or invalid.
	 */
	private static function dbtype(string $type): int {
		if (trim(strtolower($type)) === 'postgres') {
			return DBTYPE_POSTGRES;
		} else {
			return DBTYPE_MYSQL;
		}
	}

	/**
	 * Load database fixtures from the fixture path.
	 */
	private function loadFixtures() : void {
		$files = glob(__DIR__ . '/includes/dba/_files/*.yml');
		if ($files === false || empty($files)) {
			error_log('[-] ' . __METHOD__ . ': No fixtures found! :(');
		}
		array_walk($files, fn($file) => $this->loadFixture($file));
	}

	/**
	 * Load database fixtures from a specific file.
	 *
	 * The file must be a yaml file named the same as the table in the database
	 * it should populate.
	 *
	 * The file also need to have a root key with the same name as the table.
	 * Under which it contains an array of rows that should be inserted into
	 * the db table.
	 *
	 * @param string $file	The path and filename of the fixture to load.
	 *						The path name is relative to the current working
	 *						directory of the process, which should normally
	 *						be the Hubzilla root directory.
	 */
	public function loadFixture($file) : void {
		$table_name = basename($file, '.yml');
		$data = yaml_parse_file($file)[$table_name];

		foreach ($data as $entry) {
			$row = DBA::$dba->insert($table_name, $entry);
			$this->fixtures[$table_name][] = $row;
		}
	}
}

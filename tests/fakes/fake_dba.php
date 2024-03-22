<?php
namespace Zotlabs\Tests\Fakes;

require_once 'include/dba/dba_pdo.php';

/**
 * Fake dba_driver implementation.
 *
 * This is a subclass of the dba_pdo class, that essentially lets us inject a
 * stub for the PDO class that is the actual database driver.
 */
class FakeDba extends \dba_pdo {
	public function __construct($stub) {
		$this->db = $stub;
		$this->connected = true;
	}
}


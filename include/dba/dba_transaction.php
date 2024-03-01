<?php
/**
 * Class to represent a database transaction.
 *
 * A database transaction is initiated upon construction of an object of this
 * class. The transaction will be automatically rolled back upon destruction
 * unless it has been explicitly committed by calling the `commit` method.
 *
 * Wrapping multiple database operation within a transaction ensures that all
 * (or none) of the operations are successfully completed at the same time.
 *
 * If a transaction is already active when constructing an object of this
 * class, it will _not_ try to initiate a transaction, but constructs an object
 * that will in practice be a stub. This prevents that "nested" transactions
 * will cause problems with the existing active transaction.
 *
 * It also means that any rollbacks or commits perfomed on the "nested"
 * transaction will be ignored, and postponed to the outer transaction is
 * committed or rolled back.
 *
 * Also note that any modification to the database schema will implicitly
 * commit active transactions in most cases, so be careful about relying on
 * transactions in those cases.
 *
 * @Note This class assumes the actual underlying database driver is PDO.
 */
class DbaTransaction {
	private bool $committed = false;
	private bool $active = false;

	/**
	 * Creates a database transaction object.
	 *
	 * If a transaction is already active for this db connection,
	 * no transaction is initiated, and the constructed object will
	 * not perform any commit or rollback actions.
	 */
	public function __construct(private dba_driver $dba) {
		if (! $this->dba->db->inTransaction()) {
			$this->active = $this->dba->db->beginTransaction();
		}
	}

	/**
	 * Roll back the transaction if it is active and not already committed.
	 */
	public function __destruct() {
		if ($this->active && ! $this->committed) {
			$this->dba->db->rollBack();
		}
	}

	/**
	 * Commit the transaction if active.
	 *
	 * This will also mark the transaction as committed, preventing it from
	 * being attempted rolled back on destruction.
	 */
	public function commit(): void {
		if ($this->active && ! $this->committed) {
			$this->committed = $this->dba->db->commit();
		}
	}
}

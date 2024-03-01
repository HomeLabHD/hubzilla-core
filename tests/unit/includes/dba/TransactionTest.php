<?php
/*
 * Copyright (c) 2024 Hubzilla
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

require_once 'tests/fakes/fake_dba.php';
require_once 'include/dba/dba_transaction.php';

use \PHPUnit\Framework\TestCase;
use \Zotlabs\Tests\Fakes\FakeDba;

/**
 * Test database transactions.
 *
 * This class subclass the base PHPUnit TestCase class, since we do _not_
 * want a real database connection for these tests. We're testing functionality
 * of the database adapter itself, so we choose to stub the underlying db driver
 * to be able to assert that the adapter behaves as it should.
 */
class DbaTransactionTest extends TestCase {
	private $pdo_stub;

	public function setUp(): void {
		$this->pdo_stub = $this->createStub(PDO::class);
	}


	/**
	 * Test that creating a DbaTransaction object initiates a database transaction.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function test_transaction_initialized_on_construction(): void {
		// Stub PDO::inTransaction()
		// Expect that it's called once, and return false to simulate that no
		// transactions are active.
		$this->pdo_stub
			->expects($this->once())
			->method('inTransaction')
			->willReturn(false);

		// Stub PDO::beginTransaction to ensure that it is being called.
		$this->pdo_stub
			->expects($this->once())
			->method('beginTransaction')
			->willReturn(true);

		$dba = new FakeDba($this->pdo_stub);

		$transaction = new DbaTransaction($dba);
	}

	/**
	 * Test that a transaction is rolled back when the DbaTransaction object
	 * is destroyed.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function test_uncommitted_transaction_is_rolled_back_on_destruction(): void {
		// Stub PDO::inTransaction()
		// Expect that it's called once, and return false to simulate that no
		// transactions are active.
		$this->pdo_stub
			->expects($this->once())
			->method('inTransaction')
			->willReturn(false);

		// Stub PDO::beginTransaction to ensure that it is being called.
		$this->pdo_stub
			->expects($this->once())
			->method('beginTransaction')
			->willReturn(true);

		// Stub PDO::rollBack to make sure we test it is being called.
		$this->pdo_stub
			->expects($this->once())
			->method('rollBack')
			->willReturn(true);

		$dba = new FakeDba($this->pdo_stub);

		$transaction = new DbaTransaction($dba);
	}

	/**
	 * Test that a committed transaction is not rolled back when the
	 * DbaTransaction object goes out of scope.
	 */
	public function test_committed_transaction_is_not_rolled_back(): void {
		// Stub PDO::inTransaction()
		// Return false to simulate that no transaction is active when called.
		$this->pdo_stub
			->expects($this->once())
			->method('inTransaction')
			->willReturn(false);

		// Stub PDO::beginTransaction to ensure that it is being called.
		$this->pdo_stub
			->expects($this->once())
			->method('beginTransaction')
			->willReturn(true);

		// Stub PDO::rollBack to ensure it is _not_ called
		$this->pdo_stub
			->expects($this->never())
			->method('rollBack');

		// Stub PDO::commit to make the test check that it is being called
		$this->pdo_stub
			->expects($this->once())
			->method('commit')
			->willReturn(true);

		$dba = new FakeDba($this->pdo_stub);

		$transaction = new DbaTransaction($dba);
		$transaction->commit();
	}

	/**
	 * Test that commiting a transaction more than once is a no-op.
	 */
	public function test_that_committing_an_already_committed_transaction_does_nothing(): void {
		// Stub PDO::inTransaction()
		// Return false to simulate that no transaction is active when called.
		$this->pdo_stub
			->expects($this->once())
			->method('inTransaction')
			->willReturn(false);

		// Stub PDO::beginTransaction to ensure that it is being called.
		$this->pdo_stub
			->expects($this->once())
			->method('beginTransaction')
			->willReturn(true);

		// Stub PDO::rollBack to ensure it is _not_ called
		$this->pdo_stub
			->expects($this->never())
			->method('rollBack');

		// Stub PDO::commit to make the test check that it is being called
		$this->pdo_stub
			->expects($this->once())
			->method('commit')
			->willReturn(true);

		$dba = new FakeDba($this->pdo_stub);

		$transaction = new DbaTransaction($dba);
		$transaction->commit();
		$transaction->commit();
	}

	/**
	 * Test simulating constructing a DbaTransaction object when a transaction
	 * is already active.
	 *
	 * This should _not_ initiate an actual DB transaction, not call the rollBack
	 * method on destruction.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function test_that_nesting_a_transaction_does_not_create_a_new_transaction_in_db(): void {
		// Stub PDO::inTransaction()
		// We simulate that a transaction is already active, by returning true from
		// this method.
		$this->pdo_stub
			->expects($this->once())
			->method('inTransaction')
			->willReturn(true);

		// Stub PDO::beginTransaction
		// Since a transaction is already active, we should _not_ initiate
		// a new transaction when the DbaTransaction object is constructed.
		$this->pdo_stub
			->expects($this->never())
			->method('beginTransaction');

		// Stub PDO::rollBack to ensure it is _not_ called
		$this->pdo_stub
			->expects($this->never())
			->method('rollBack');

		$dba = new FakeDba($this->pdo_stub);

		$transaction = new DbaTransaction($dba);
	}
}

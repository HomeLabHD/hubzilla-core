<?php
/**
 * Tests for `includes/dba_pdo.php`.
 *
 * SPDX-FileCopyrightText: 2024 Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Tests\Unit\includes;

use DBA;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use Zotlabs\Tests\Unit\UnitTestCase;

class DbaPdoTest extends UnitTestCase
{
	public function testInsertingRowWithRturningClauseReturnsInsertedRow(): void
	{
		// MySQL does not support the `returning` clause, so we skip the test
		// for that DB backend.
		$this->skipIfMySQL();

		// Let's manually insert a row in the config table.
		// This is just because it's a conventient table to test
		// against
		$res = q(<<<SQL
			INSERT INTO config (cat, k, v)
				VALUES ('test', 'a key', 'A value')
				RETURNING *
			SQL);

		$this->assertIsArray($res);
		$this->assertIsArray($res[0]);
		$this->assertTrue($res[0]['id'] > 0);
		$this->assertEquals('test', $res[0]['cat']);
		$this->assertEquals('a key', $res[0]['k']);
		$this->assertEquals('A value', $res[0]['v']);
	}

	#[DataProvider('insertRowProvider')]
	public function testInsertRow(string $table, array $data, string $id): void
	{
		$res = DBA::$dba->insert($table, $data, $id);

		$this->assertIsArray($res);

		// Make sure the result contains the expected id
		$this->assertArrayHasKey($id, $res);

		foreach ($data as $key => $value) {
			$this->assertEquals($value, $res[$key]);
		}
	}

	#[DataProvider('insertRowProvider')]
	public function testInsertShouldReturnFalseIfInsertFails(
		string $table,
		array $data,
		string $id
		): void
	{
		$res1 = DBA::$dba->insert($table, $data, $id);
		$this->assertIsArray($res1);

		// Inserting the same row again should fail.
		$res2 = DBA::$dba->insert($table, $data, $id);
		$this->assertFalse($res2);
	}

	/**
	 * Dataprovider for testInertRow.
	 *
	 * @return array	An array of [ $table, $data, $id ] elements.
	 */
	public static function insertRowProvider(): array
	{
		return [
			'table with numeric primary id' => [
				'config',
				[ 'cat' => 'test', 'k' => 'a key', 'v' => 'A value' ],
				'id',
			],
			'table with text primary id' => [
				'cache',
				[ 'k' => 'some key', 'v' => 'cached value', 'updated' => date('Y-m-d H:i:s')],
				'k',
			],
		];
	}

	public function testUpdateRow(): void
	{
		// Let's fetch a row from the config table
		$res = q("SELECT * FROM config WHERE cat = 'system' AND k = 'baseurl'");

		$this->assertIsArray($res);
		$this->assertIsArray($res[0]);

		$row = $res[0];

		// Update the baseurl
		$updated = DBA::$dba->update(
			'config',
			[ 'v' => 'https://some.other_site.test/' ],
			'id',
			$row['id']
		);

		$this->assertTrue($updated);

		// Verify that the record was updated
		$updated_res = q("SELECT * FROM config WHERE cat = 'system' AND k = 'baseurl'");
		$this->assertIsArray($updated_res);

		$updated_row = $updated_res[0];

		$this->assertIsArray($updated_row);
		$this->assertEquals($row['id'], $updated_row['id']);
		$this->assertEquals('system', $updated_row['cat']);
		$this->assertEquals('baseurl', $updated_row['k']);
		$this->assertEquals('https://some.other_site.test/', $updated_row['v']);
	}

	/**
	 * Mark the test as skipped if the current db is MySQL.
	 */
	private function skipIfMySQL(): void {
		$driver = DBA::$dba->db->getAttribute(PDO::ATTR_DRIVER_NAME);
		$version = DBA::$dba->db->getAttribute(PDO::ATTR_SERVER_VERSION);

		if ($driver === 'mysql' && stripos($version, 'mariadb') === false) {
			$this->markTestSkipped("RETURNING clause not supported for {$driver}");
		}

	}
}

<?php
/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 *
 * SPDX-License-Identifier: MIT
 */

namespace Zotlabs\Entity;

use DBA;
use PDO;

abstract class Entity
{
	abstract protected static function fromDbRow(array $columns): static;

	protected static function selectAll(array $conditions): array
	{
		[$where, $values] = self::parseConditions($conditions);

		$stmt = DBA::$dba->db->prepare(
			'select * from ' . static::$TABLE_NAME . ' where '
			. implode(' and ', $where)
		);

		$stmt->execute($values);

		$results = [];
		foreach ($stmt as $row) {
			$results[] = static::fromDbRow($row);
		}

		return $results;
	}

	public function insert(array $data): static
	{
		$result = DBA::$dba->insert(static::$TABLE_NAME, $data);
		return static::fromDbRow($result);
	}

	public function delete(array $conditions): void
	{
		[$where, $values] = self::parseConditions($conditions);

		$stmt = DBA::$dba->db->prepare(
			'delete from ' . static::$TABLE_NAME . ' where '
			. implode(' and ', $where)
		);

		$stmt->execute($values);
	}

	private static function parseConditions(array $conditions): array
	{
		$where = [];
		$values = [];

		foreach ($conditions as $key => $value) {
			$where[] = "{$key} = ?";
			$values[] = $value;
		}

		return [$where, $values];
	}
}

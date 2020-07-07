<?php
/**
 * @package plutonium\database\sqlite3
 */

namespace Plutonium\Database\SQLite3;

use Plutonium\Database\AbstractDelegate;

class Delegate extends AbstractDelegate {
	public function exists() {
		$field1 = $this->quoteSymbol('type');
		$value2 = $this->quoteString('table');
		$field2 = $this->quoteSymbol('name');
		$value2 = $this->quoteString($this->_table->table_name);

		$where = "WHERE $field1 = $value1 AND $field2 = $value2";

		$table = $this->quoteSymbol('sqlite_master');

		$sql = "SELECT * FROM $table $where";

		if ($result = $this->query($sql)) {
			$exists = $result->getNumRows() > 0;
			$result->close();

			return $exists;
		}

		return false;
	}

	public function create() {
		$sql = "CREATE TABLE IF NOT EXISTS $this->table_name (\n";

		$lines = array();

		foreach ($this->_table->field_meta as $field_meta) {
			$field = $this->quoteSymbol($field_meta->name);
			$type = $this->_createFieldType($field_meta);

			if (!$type)
				continue;

			if ($field_meta->name == 'id')
				$type .= " PRIMARY KEY AUTOINCREMENT";
			elseif ($field_meta->unique)
				$type .= " UNIQUE";
			elseif ($field_meta->has('default'))
				$type .= " DEFAULT " . $this->quoteString($field_meta->default);
			elseif (!$field_meta->null)
				$type .= " NOT NULL";

			$lines[] = "$field $type";
		}

		if (!empty($lines))
			$sql .= "\t" . implode(",\n\t", $lines) . "\n";

		$sql .= ")";

		return $this->query($sql);
	}

	protected function _createFieldType($field_meta) {
		// TODO implement column types
	}

	public function modify() {
		// TODO method stub
	}
}

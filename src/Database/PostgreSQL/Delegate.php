<?php
/**
 * @package plutonium\database\postgresql
 */

namespace Plutonium\Database\PostgreSQL;

use Plutonium\Database\AbstractDelegate;

class Delegate extends AbstractDelegate {
	public function exists() {
		$table = $this->quoteSymbol('pg_class');
		$field = $this->quoteSymbol('relname');
		$value = $this->quoteString($this->_table->table_name);

		$sql = "SELECT * FROM $table WHERE $field = $value";

		if ($result = $this->query($sql)) {
			$exists = $result->getNumRows() > 0;
			$result->close();

			return $exists;
		}

		return false;
	}

	public function create() {
		$sql = "CREATE TABLE $this->table_name (";

		$lines = array();

		foreach ($this->_table->field_meta as $field_meta) {
			$field = $this->quoteSymbol($field_meta->name);
			$type = $this->_createFieldType($field_meta);

			if (!$type)
				continue;

			if (!$field_meta->null)
				$type .= " NOT NULL";

			if ($field_meta->has('default')) {
				$default = $this->quoteString($field_meta->default);

				$type .= " DEFAULT $default";
			}

			if ($field_meta->name == 'id')
				$type .= " PRIMARY KEY";
			elseif ($field_meta->unique)
				$type .= " UNIQUE";

			$lines[] = "$field $type";
		}

		if (!empty($lines))
			$sql .= "\t" . implode(",\n\t", $lines) . "\n";

		$sql .=  ")";
	}

	protected function _createFieldType($field_meta) {
		switch ($field_meta->type) {
			case 'bool':
				return 'boolean';
			case'int':
				if ($field_meta->auto) {
					$prefix = array('long' => 'big');
					return @$prefix[$field_meta->size] . 'serial';
				} else {
					$prefix = array('short' => 'small', 'long' => 'big');
					return @$prefix[$field_meta->size] . 'int';
				}
			case 'float':
				return $field_meta->size == 'long' ? 'double precision' : 'real';
			case 'string':
				return 'text';
			case 'date':
				return 'timestamp';
			default:
				return false;
		}
	}

	public function modify() {
		// TODO method stub
	}
}

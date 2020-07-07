<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

use function Plutonium\Functions\is_assoc;

abstract class AbstractDelegate {
	protected $_adapter = null;
	protected $_table   = null;

	public function __construct($table) {
		$this->_adapter = AbstractAdapter::getInstance();
		$this->_table   = $table;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'table_name':
				return $this->_adapter->quoteSymbol($this->_table->table_name);
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __call($name, $args) {
		if (method_exists($this->_adapter, $name))
			return call_user_func_array(array($this->_adapter, $name), $args);
	}

	public function select($match, $sort = null, $limit = 0, $offset = 0) {
		$return_limit = $limit;

		if (is_scalar($match))
			$limit = $return_limit = 1;

		$sql = "SELECT * FROM $this->table_name";

		if ($where = $this->_whereClause($match))
			$sql .= " $where";

		if ($order = $this->_orderClause($sort))
			$sql .= " $order";

		if ($limit = $this->_limitClause($limit, $offset))
			$sql .= " $limit";

		if ($result = $this->query($sql)) {
			$rows = array();

			foreach ($result->fetchAllAssoc() as $data)
				$rows[] = $this->_table->make($data);

			$result->close();

			return $return_limit == 1 ? $rows[0] : $rows;
		}

		return false;
	}

	public function select_xref($xref, $match) {
		$id = $this->quoteSymbol('id');

		$xref_table = $this->quoteSymbol($xref->table_name);

		foreach ($xref->table_refs as $key => $table_name) {
			if ($table_name == $this->_table->name) {
				$xref_id = $this->quoteSymbol($key . '_id');
			} else {
				$join_ref_id = $this->quoteSymbol($key . '_id');
				$xref_alias = $key;
			}
		}

		$join = "$this->table_name INNER JOIN $xref_table ON "
			  . "$this->table_name.$id = $xref_table.$xref_id";

		if (!empty($match)) {
			if (is_scalar($match)) {
				$ref_id = $this->quoteString($match);

				$where = "$xref_table.$join_ref_id = $ref_id";
			} elseif (is_array($match)) {
				$filters = array();

				if (array_key_exists('ref_id', $match)) {
					$ref_id = $this->quoteString($match['ref_id']);

					$filters[] = "$xref_table.$join_ref_id = $ref_id";

					// remove reserved key
					unset($match['ref_id']);
				}

				foreach ($match as $field => $value) {
					$field = $this->table_name . '.' . $this->quoteSymbol($field);

					if (is_array($value)) {
						$list = array();

						foreach ($value as $item)
							$list[] = $this->quoteString($item);

						$list = implode(', ', $list);

						if (!empty($list))
							$filters[] = "$field IN ($list)";
					} else {
						$value = $this->quoteString($value);

						$filters[] = "$field = $value";
					}
				}

				if (!empty($filters))
					$where = implode(' AND ', $filters);
			}
		}

		$fields = array("$this->table_name.*");
		$xref_fields = array();

		foreach ($xref->field_meta as $field_meta) {
			$is_ref = false;

			foreach ($xref->table_refs as $ref_alias => $ref_table) {
				if ($ref_alias . '_id' == $field_meta->name) {
					$is_ref = true;
					break;
				}
			}

			if (!$is_ref) {
				$field = $this->quoteSymbol($field_meta->name);
				$alias = $this->quoteSymbol('xref_' . $xref_alias . '_' . $field_meta->name);

				$fields[] = "$xref_table.$field AS $alias";

				$xref_fields[] = $this->stripSymbol($alias);
			}
		}

		$fields = implode(', ', $fields);

		$sql = "SELECT $fields FROM $join";

		if (!empty($where))
			$sql .= " WHERE $where";

		if ($result = $this->query($sql)) {
			$rows = array();

			foreach ($result->fetchAllAssoc() as $data) {
				$xref_data = array();

				foreach ($xref_fields as $xref_field) {
					$field = str_replace('xref_' . $xref_alias . '_', '', $xref_field);
					$xref_data[$xref_alias][$field] = $data[$xref_field];
				}

				$rows[] = $this->_table->make($data, $xref_data);
			}

			$result->close();

			return $rows;
		}

		return false;
	}

	public function insert($row) {
		$fields = array();
		$values = array();

		foreach ($this->_table->field_names as $field) {
			if (!is_null($row->$field) && $field != 'id') {
				$fields[] = $this->quoteSymbol($field);
				$values[] = $this->quoteString($row->$field);
			}
		}

		if ($this->_table->table_meta->timestamps) {
			$fields[] = $this->quoteSymbol('created');
			$values[] = 'NOW()';
		}

		if (!empty($fields) && !empty($values)) {
			$fields = implode(', ', $fields);
			$values = implode(', ', $values);

			$sql = "INSERT INTO $this->table_name ($fields) VALUES ($values)";

			if ($this->query($sql)) {
				$row->id = $this->getInsertId();

				return true;
			}
		}

		return false;
	}

	public function update($row) {
		$fields = array();

		foreach ($this->_table->field_names as $field) {
			if (!is_null($row->$field) && $field != 'id') {
				$fields[] = $this->quoteSymbol($field) . ' = '
						  . $this->quoteString($row->$field);
			}
		}

		if ($this->_table->table_meta->timestamps) {
			$fields[] = $this->quoteSymbol('updated') . ' = NOW()';
		}

		if (!empty($fields)) {
			$fields = implode(', ', $fields);

			$where = $this->_whereClause($row->id);

			$sql = "UPDATE $this->table_name SET $fields $where";

			return $this->query($sql);
		}

		return false;
	}

	public function delete($id) {
		$where = $this->_whereClause($id);

		$sql = "DELETE FROM $this->table_name $where";

		return $this->query($sql);
	}

	protected function _whereClause($match) {
		if (empty($match)) {
			return false;
		} elseif (is_scalar($match)) {
			$filter = $this->_whereEquals('id', $match);
		} elseif (is_assoc($match)) {
			$filters = array();

			foreach ($match as $field => $value) {
				$filters[] = is_array($value)
					       ? $this->_whereInList($field, $value)
						   : $this->_whereEquals($field, $value);
			}

			if (!empty($filters))
				$filter = implode(' AND ', $filters);
		} elseif (is_array($match)) {
			$filter = $this->_whereInList('id', $match);
		}

		if (!empty($filter))
			return "WHERE $filter";

		return false;
	}

	protected function _whereEquals($field, $value) {
		$field = $this->quoteSymbol($field);
		$value = $this->quoteString($value);

		return "$field = $value";
	}

	protected function _whereInList($field, $values) {
		$list = array();

		foreach ($values as $value)
			$list[] = $this->quoteString($value);

		$field = $this->quoteSymbol($field);
		$list  = implode(', ', $list);

		return "$field IN ($list)";
	}

	protected function _orderClause($fields) {
		if (empty($fields)) return false;

		$list = array();

		foreach ($fields as $key => $value) {
			if (is_string($key)) {
				$field = $this->quoteSymbol($key);
				$order = strtoupper($value) == 'DESC' ? 'DESC' : 'ASC';
			} else {
				$field = $this->quoteSymbol($value);
				$order = 'ASC';
			}

			$list[] = "$field $order";
		}

		$list = implode(', ', $list);

		return "ORDER BY $list";
	}

	protected function _limitClause($limit = 0, $offset = 0) {
		$limit  = intval($limit);
		$offset = intval($offset);

		if ($limit > 0) {
			$sql = "LIMIT $limit";

			if ($offset > 0)
				$sql .= "OFFSET $offset";

			return $sql;
		}

		return false;
	}

	abstract public function exists();

	abstract public function create();

	abstract public function modify();

	public function drop() {
		$sql = "DROP TABLE IF EXISTS $this->table_name";

		return $this->query($sql);
	}
}

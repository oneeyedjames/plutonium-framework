<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

use function Plutonium\Functions\is_assoc;

use Plutonium\AccessObject;

class Row {
	protected $_table = null;
	protected $_data  = array();
	protected $_refs  = array();
	protected $_revs  = array();
	protected $_xrefs = array();

	protected $_xref_data = null;

	public function __construct($table, $data = null, $xref_data = null) {
		$this->_table = $table;
		$this->_data  = array_fill_keys($table->field_names, null);
		$this->_refs  = array_fill_keys(array_keys($table->table_refs), null);
		$this->_revs  = array_fill_keys(array_keys($table->table_revs), null);
		$this->_xrefs = array_fill_keys(array_keys($table->table_xrefs), null);

		$this->_xref_data = new AccessObject();

		$this->bind($data);

		if (!empty($xref_data))
			$this->_bind_xref($xref_data);
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		if (array_key_exists($key, $this->_data)) {
			return $this->_data[$key];
		} elseif (array_key_exists($key, $this->_refs)) {
			if (is_null($this->_refs[$key])) {
				$ref_id = $this->_data[$key . '_id'];

				if (!empty($ref_id)) {
					$ref_table = $this->_table->table_refs[$key];
					$ref_table = Table::getInstance($ref_table);

					$this->_refs[$key] = $ref_table->find($ref_id);

					if (!is_null($row = $this->_refs[$key])) {
						// TODO add this row to ref_row's revs array
					}
				}
			}

			return $this->_refs[$key];
		} elseif (array_key_exists($key, $this->_revs)) {
			if (is_null($this->_revs[$key])) {
				$rev_id = $this->_data['id'];

				$rev_alias = $this->_table->table_revs[$key]->alias;
				$rev_table = $this->_table->table_revs[$key]->table;
				$rev_table = Table::getInstance($rev_table);

				$this->_revs[$key] = $rev_table->find(array(
					$rev_alias . '_id' => $rev_id
				));

				foreach ($this->_revs[$key] as $row)
					$row->_refs[$rev_alias] = $this;
			}

			return $this->_revs[$key];
		} elseif (array_key_exists($key, $this->_xrefs)) {
			if (is_null($this->_xrefs[$key])) {
				$this->_xrefs[$key] = $this->__call($key, array());

				/* $table = $this->_table->table_xref[$key];

				foreach ($table->table_refs as $alias => $ref_table) {
					if ($alias != $key && $ref_table == $this->_table->name) {
						$xref_id = $alias . '_id';
						break;
					}
				}

				if (!empty($xref_id)) {
					$this->_xrefs[$key] = $table->find(array(
						$xref_id => $this->_data['id']
					));
				} */
			}

			return $this->_xrefs[$key];
		} elseif ($key == 'xref') {
			return $this->_xref_data;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		if (array_key_exists($key, $this->_data)) {
			$this->_data[$key] = $value;
		} elseif (array_key_exists($key, $this->_refs)) {
			$this->_refs[$key] = $value;
			$this->_data[$key . '_id'] = @$value->id;

			// TODO add this row to ref_row's revs array
		} elseif (array_key_exists($key, $this->_revs)) {
			$this->_revs[$key] = $value;

			// TODO set this row as each rev_row's ref
			foreach ($value as $row) {
				// $row->_refs[$rev_alias] = $value;
				// $row->_data[$rev_alias . '_id'] = @$value->id;
			}
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		return array_key_exists($key, $this->_data);
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {
		unset($this->_data[$key]);
	}

	/**
	 * TODO work out fetch on cross-reference
	 * @ignore magic method
	 */
	public function __call($name, $args) {
		if (array_key_exists($name, $this->_xrefs)) {
			$xref = $this->_table->table_xrefs[$name];
			$xref_args = empty($args) ? $this->_data['id'] : $args[0];

			if (is_array($xref_args)) $xref_args['ref_id'] = $this->_data['id'];

			foreach ($xref->table_refs as $ref_alias => $ref_table) {
				if ($ref_alias != $name && $ref_table == $this->_table->name) {
					$xref_name = $ref_alias;
				} else {
					$xref_id = $ref_alias . '_id';
					$table = Table::getInstance($ref_table);
				}
			}

			return $table->find_xref($xref_name, $xref_args);
		}

		return null;
	}

	public function bind($data) {
		if (is_assoc($data) || $data instanceof AccessObject)
			foreach ($data as $key => $value) $this->$key = $value;
	}

	protected function _bind_xref($xref_data) {
		if (is_assoc($xref_data)) {
			foreach ($xref_data as $xref => $data) {
				$this->_xref_data[$xref] = $this->_table->table_xrefs[$xref]->make($data);
			}
		}
	}

	public function save() {
		return $this->_table->save($this);
	}

	public function delete() {
		return $this->_table->delete($this->id);
	}

	public function toArray() {
		return $this->_data;
	}

	public function toObject() {
		return (object) $this->_data;
	}
}

<?php

namespace Plutonium\Application;

use Plutonium\Database\Table;

class Model {
	protected $_name   = null;
	protected $_table  = null;
	protected $_module = null;

	public function __construct($args) {
		$this->_name   = $args->name;
		$this->_module = $args->module;
	}

	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'module':
				return $this->_module;
		}
	}

	public function find($args = null, $sort = null, $limit = 0, $offset = 0) {
		return $this->getTable()->find($args, $sort, $limit, $offset);
	}

	public function save($data) {
		if ($this->validate($data)) {
			$row = $this->getTable()->make($data);

			return $row->save() ? $row : false;
		}

		return false;
	}

	public function delete($id) {
		$row = $this->getTable()->find($id);

		return $row->delete();
	}

	/**
	 * Override this behavior in child classes
	 */
	public function validate(&$data) {
		$table = $this->getTable();

		if (in_array('created', $table->field_names) &&
			in_array('updated', $table->field_names)) {
			$data['updated'] = date('Y-m-d H:i:s', time());
			if (empty($data['id'])) $data['created'] = $data['updated'];
		}

		foreach (array_keys($table->table_refs) as $ref_name) {
			if (isset($data[$ref_name])) {
				$data[$ref_name . '_id'] = intval($data[$ref_name]);
				unset($data[$ref_name]);
			}
		}

		return true;
	}

	public function getTable() {
		if (is_null($this->_table))
			$this->_table = Table::getInstance($this->_name, $this->_module->name);

		return $this->_table;
	}
}

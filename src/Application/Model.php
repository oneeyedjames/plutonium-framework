<?php
/**
 * @package plutonium\application
 */

namespace Plutonium\Application;

use Plutonium\Database\Table;

/**
 * @property-read string $name Resource name
 * @property-read object $module The active Module object
 */
class Model {
	/**
	 * @ignore internal variable
	 */
	protected $_name = null;

	/**
	 * @ignore internal variable
	 */
	protected $_table = null;

	/**
	 * @ignore internal variable
	 */
	protected $_module = null;

	/**
	 * Expected args
	 *   - name: resource name
	 *   - module: active Module object
	 * @param object $args AccessObject
	 */
	public function __construct($args) {
		$this->_name   = $args->name;
		$this->_module = $args->module;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'module':
				return $this->_module;
		}
	}

	/**
	 * Attempts to retrieve database records matching the given parameters.
	 * @param array $args OPTIONAL Fields and values to match
	 * @param array $sort OPTIONAL Fields to sort by
	 * @param integer $limit OPTIONAL Maximum number or records to return
	 * @param integer $offset OPTIONAL Number of leading records to ignore
	 * @return array Row objects
	 */
	public function find($args = null, $sort = null, $limit = 0, $offset = 0) {
		return $this->getTable()->find($args, $sort, $limit, $offset);
	}

	/**
	 * Attempts to insert or update a database record.
	 * @param array $data Key-value pairs
	 * @return boolean TRUE on sucess, FALSE on failure
	 */
	public function save($data) {
		if ($this->validate($data)) {
			$row = $this->getTable()->make($data);

			return $row->save() ? $row : false;
		}

		return false;
	}

	/**
	 * Attempts to delete a database record.
	 * @param mixed $id Primary key value
	 * @return boolean TRUE on sucess, FALSE on failure
	 */
	public function delete($id) {
		$row = $this->getTable()->find($id);

		return $row->delete();
	}

	/**
	 * Populates timestamps and foreign keys and returns whether data is valid.
	 * @param array $data Key-value pairs
	 * @return boolean Whether the data is valid
	 */
	public function validate(&$data) {
		$table = $this->getTable();

		foreach (array_keys($table->table_refs) as $ref_name) {
			if (isset($data[$ref_name])) {
				$data[$ref_name . '_id'] = intval($data[$ref_name]);
				unset($data[$ref_name]);
			}
		}

		return true;
	}

	/**
	 * Returns the backing database Table object for this model.
	 * @return object Table object
	 */
	public function getTable() {
		if (is_null($this->_table))
			$this->_table = Table::getInstance($this->_name, $this->_module->name);

		return $this->_table;
	}
}

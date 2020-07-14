<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

/**
 * Base class for all vendor-specific result sets. This class only supports
 * sequential access of the returned records.
 */
abstract class AbstractResult {
	/**
	 * @ignore internal variable
	 */
	protected $_result = null;

	/**
	 * @ignore internal method
	 */
	public function __construct($result) {
		$this->_result = $result;
	}

	/**
	 * Returns number of fields included in result set.
	 * @return integer Number of fields
	 */
	abstract public function getNumFields();

	/**
	 * Resets the internal pointer to the start of the result set.
	 */
	abstract public function reset();

	/**
	 * Closes the result set and frees the data pointer.
	 */
	abstract public function close();

	/**
	 * Returns the next record as an indexed array.
	 * @return array Indexed array
	 */
	abstract public function fetchArray();

	/**
	 * Returns the next record as an associative array.
	 * @return array Associative array
	 */
	abstract public function fetchAssoc();

	/**
	 * Returns the next record as an object.
	 * @return object Plain ol' PHP object
	 */
	abstract public function fetchObject();

	/**
	 * Returns the next record as the given data type.
	 * @param string $type Data type: 'array', 'assoc', or 'object'
	 * @return mixed Database record as given data type
	 */
	public function fetch($type = 'array') {
		switch ($type) {
			case 'array':
				return $this->fetchArray();
			case 'assoc':
				return $this->fetchAssoc();
			case 'object':
				return $this->fetchObject();
			default:
				return false;
		}
	}

	/**
	 * Returns all records as array of the given data type.
	 * @param string $type Data type: 'array', 'assoc', or 'object'
	 * @return array Database records as given data type
	 */
	public function fetchAll($type = 'array') {
		$this->reset();

		$rows = array();

		while ($row = $this->fetch($type))
			$rows[] = $row;

		return $rows;
	}

	/**
	 * Returns all records as indexed array of indexed arrays.
	 * @return array
	 */
	public function fetchAllArray() {
		return $this->fetchAll('array');
	}

	/**
	 * Returns all records as indexed array of associative arrays.
	 * @return array
	 */
	public function fetchAllAssoc() {
		return $this->fetchAll('assoc');
	}

	/**
	 * Returns all records as indexed array of objects.
	 * @return array
	 */
	public function fetchAllObject() {
		return $this->fetchAll('object');
	}
}

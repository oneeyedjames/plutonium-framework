<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

/**
 * Extended base class with support for random access of the returned records.
 */
abstract class SeekableResult extends AbstractResult {
	/**
	 * Returns the number of returned records.
	 * @return integer Number of records
	 */
	abstract public function getNumRows();

	/**
	 * Moves the internal pointer to the chosen offset.
	 * @param integer $row Offset between 0 and number of records
	 */
	abstract public function seek($row);

	/**
	 * Resets the internal pointer to the start of the result set.
	 */
	public function reset() {
		return $this->seek(0);
	}

	/**
	 * Returns the value found at the  given row and field offsets. For the
	 * default implementation, the internal pointer will be moved to the row
	 * offset.
	 * @param integer $row Row offset
	 * @param integer $field Field offset
	 * @return mixed Value found at row and field offsets
	 */
	public function fetchResult($row = 0, $field = 0) {
		if ($this->_canFetchResult($row, $field) && $this->seek($row)) {
			trigger_error('Default implementation of SeekableResult::fetchResult() alters internal pointer', E_USER_NOTICE);
			return $this->fetchArray()[$field];
		}

		return false;
	}

	/**
	 * @ignore interal method
	 */
	protected function _canFetchResult($row = 0, $field = 0) {
		if ($row < 0 || $row >= $this->getNumRows()) {
			trigger_error("Row index out of bounds: $row", E_USER_WARNING);
			return false;
		}

		if ($field < 0 || $field >= $this->getNumFields()) {
			trigger_error("Field index out of bounds: $field", E_USER_WARNING);
			return false;
		}

		return true;
	}
}

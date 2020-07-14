<?php
/**
 * @package plutonium\database\mysql
 */

namespace Plutonium\Database\MySQL;

use Plutonium\Database\SeekableResult;

/**
 * @ignore vendor-specific implementation
 */
class Result extends SeekableResult {
	public function getNumFields() {
		return mysql_num_fields($this->_result);
	}

	public function getNumRows() {
		return mysql_num_rows($this->_result);
	}

	public function close() {
		return mysql_free_result($this->_result);
	}

	public function seek($row) {
		return mysql_data_seek($this->_result, $row);
	}

	public function fetchArray() {
		return mysql_fetch_row($this->_result);
	}

	public function fetchAssoc() {
		return mysql_fetch_assoc($this->_result);
	}

	public function fetchObject() {
		return mysql_fetch_object($this->_result);
	}

	/**
	 * Overrides default implementation in SeekableResult class.
	 */
	public function fetchResult($row = 0, $field = 0) {
		if ($this->_canFetchResult($row, $field)) {
			return mysql_result($this->_result, $row, $field);
		}

		return false;
	}
}

<?php
/**
 * @package plutonium\database\mysqli
 */

namespace Plutonium\Database\MySQLi;

use Plutonium\Database\SeekableResult;

/**
 * @ignore vendor-specific implementation
 */
class Result extends SeekableResult {
	public function getNumFields() {
		return $this->_result->field_count;
	}

	public function getNumRows() {
		return $this->_result->num_rows;
	}

	public function close() {
		return $this->_result->free();
	}

	public function seek($row) {
		return $this->_result->data_seek($row);
	}

	public function fetchArray() {
		return $this->_result->fetch_row();
	}

	public function fetchAssoc() {
		return $this->_result->fetch_assoc();
	}

	public function fetchObject() {
		return $this->_result->fetch_object();
	}
}

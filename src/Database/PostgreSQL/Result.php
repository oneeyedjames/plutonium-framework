<?php

namespace Plutonium\Database\PostgreSQL;

use Plutonium\Database\SeekableResult;

class Result extends SeekableResult {
	public function getNumFields() {
		return pg_num_fields($this->_result);
	}

	public function getNumRows() {
		return pg_num_rows($this->_result);
	}

	public function close() {
		return pg_free_result($this->_result);
	}

	public function seek($row) {
		return pg_result_seek($this->_result, $row);
	}

	public function fetchArray() {
		return pg_fetch_row($this->_result);
	}

	public function fetchAssoc() {
		return pg_fetch_assoc($this->_result);
	}

	public function fetchObject() {
		return pg_fetch_object($this->_result);
	}

	/**
	 * Overrides default implementation in SeekableResult trait.
	 */
	public function fetchResult($row = 0, $field = 0) {
		if ($this->_canFetchResult($row, $field)) {
			return pg_fetch_result($this->_result, $row, $field);
		}

		return false;
	}
}

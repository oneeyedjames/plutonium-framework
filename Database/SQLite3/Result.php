<?php

namespace Plutonium\Database\SQLite3;

use Plutonium\Database\AbstractResult;

class Result extends AbstractResult {
	protected $_rows = array();

	public function reset() {
		return $this->_result->reset();
	}

	public function close() {
		return $this->_result->finalize();
	}

	public function seek($num) {
		// raise error

		return false;
	}

	public function getNumFields() {
		return $this->_result->numColumns();
	}

	public function getNumRows() {
		$this->_cacheRows();

		return count($this->_rows);
	}

	public function fetchArray() {
		return $this->_result->fetchArray(SQLITE3_NUM);
	}

	public function fetchAssoc() {
		return $this->_result->fetchArray(SQLITE3_ASSOC);
	}

	public function fetchObject() {
		return (object) $this->_result->fetchArray(SQLITE3_ASSOC);
	}

	public function fetchResult($row = 0, $field = 0) {
		$this->_cacheRows();

		return $this->_rows[$row][$field];
	}

	protected function _cacheRows() {
		if (empty($this->_rows)) {
			$this->_result->reset();

			while ($row = $this->_result->fetchArray(SQLITE3_BOTH) {
				$this->_rows[] = $row;
			}
		}
	}
}

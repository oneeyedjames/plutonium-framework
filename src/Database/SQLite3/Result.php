<?php
/**
 * @package plutonium\database\sqlite3
 */

namespace Plutonium\Database\SQLite3;

use Plutonium\Database\AbstractResult;

class Result extends AbstractResult {
	public function getNumFields() {
		return $this->_result->numColumns();
	}

	public function reset() {
		return $this->_result->reset();
	}

	public function close() {
		return $this->_result->finalize();
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
}

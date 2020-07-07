<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

abstract class SeekableResult extends AbstractResult {
	abstract public function getNumRows();

	abstract public function seek($row);

	public function reset() {
		return $this->seek(0);
	}

	public function fetchResult($row = 0, $field = 0) {
		if ($this->_canFetchResult($row, $field) && $this->seek($row)) {
			trigger_error('Default implementation of SeekableResult::fetchResult() alters internal pointer', E_USER_NOTICE);
			return $this->fetchArray()[$field];
		}

		return false;
	}

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

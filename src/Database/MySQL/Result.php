<?php

namespace Plutonium\Database\MySQL;

use Plutonium\Database\AbstractResult;

class Result extends AbstractResult {
	public function close() {
		return mysql_free_result($this->_result);
	}

	public function seek($num) {
		return mysql_data_seek($this->_result, $num);
	}

	public function getNumFields() {
		return mysql_num_fields($this->_result);
	}

	public function getNumRows() {
		return mysql_num_rows($this->_result);
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

	public function fetchResult($row = 0, $field = 0) {
		if (mysql_num_rows($this->_result) <= $row ||
			mysql_num_fields($this->_result) <= $field) return false;

		return mysql_result($this->_result, $row, $field);
	}
}

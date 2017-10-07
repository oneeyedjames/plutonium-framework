<?php

namespace Plutonium\Database\MySQLi;

use Plutonium\Database\AbstractResult;

class Result extends AbstractResult {
	public function close() {
		return $this->_result->free();
	}

	public function seek($num) {
		return $this->_result->data_seek($num);
	}

	public function getNumFields() {
		return $this->_result->field_count;
	}

	public function getNumRows() {
		return $this->_result->num_rows;
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

	public function fetchResult($row = 0, $field = 0) {
		if ($this->_result->num_rows <= $row ||
			$this->_result->field_count <= $field) return false;

		if ($this->_result->data_seek($row))
			return $this->_result->fetch_row()[$field];

		return false;
	}
}

<?php

namespace Plutonium\Database;

abstract class AbstractResult {
	protected $_result = null;

	public function __construct($result) {
		$this->_result = $result;
	}

	public function reset() {
		return $this->seek(0);
	}

	abstract public function close();
	abstract public function seek($num);

	abstract public function getNumFields();
	abstract public function getNumRows();

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

	public function fetchAll($type = 'array') {
		if ($this->getNumRows() > 0)
			$this->reset();

		$rows = array();

		while ($row = $this->fetch($type))
			$rows[] = $row;

		return $rows;
	}

	abstract public function fetchArray();
	abstract public function fetchAssoc();
	abstract public function fetchObject();
	abstract public function fetchResult($row = 0, $field = 0);

	public function fetchAllArray() {
		return $this->fetchAll('array');
	}

	public function fetchAllAssoc() {
		return $this->fetchAll('assoc');
	}

	public function fetchAllObject() {
		return $this->fetchAll('object');
	}
}

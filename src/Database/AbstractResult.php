<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

abstract class AbstractResult {
	protected $_result = null;

	public function __construct($result) {
		$this->_result = $result;
	}

	abstract public function getNumFields();

	abstract public function reset();
	abstract public function close();

	abstract public function fetchArray();
	abstract public function fetchAssoc();
	abstract public function fetchObject();

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
		$this->reset();

		$rows = array();

		while ($row = $this->fetch($type))
			$rows[] = $row;

		return $rows;
	}

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

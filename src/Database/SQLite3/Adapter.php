<?php
/**
 * @package plutonium\database\sqlite3
 */

namespace Plutonium\Database\SQLite3;

use Plutonium\Database\AbstractAdapter;

class Adapter extends AbstractAdapter {
	public function connect() {
		$this->_connection = new SQLite3($this->_config->dbfile);

		return is_object($this->_connection);
	}

	public function close() {
		return $this->_connection->close();
	}

	public function query($sql) {
		$result = $this->_connection->query($sql);

		if (is_object($result))
			return new Result($result);

		return $result;
	}

	public function getAffectedRows() {
		return -1;
	}

	public function getInsertId() {
		return $this->_connection->lastInsertRowID();
	}

	public function getErrorNum() {
		return $this->_connection->lastErrorCode();
	}

	public function getErrorMsg() {
		return $this->_connection->lastErrorMsg();
	}

	public function escapeString($str) {
		return $this->_connection->escapeString($str);
	}

	public function quoteString($str) {
		return "'" . $this->escapeString($str) . "'";
	}

	public function quoteSymbol($sym) {
		return '"' . $sym . '"';
	}

	public function stripString($str) {
		return trim($str, "'");
	}

	public function stripSymbol($sym) {
		return trim($sym, '"');
	}
}

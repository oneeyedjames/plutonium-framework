<?php

namespace Plutonium\Database\PostgreSQL;

use Plutonium\Database\AbstractAdapter;

class Adapter extends AbstractAdapter {
	protected $_result = null;

	public function connect() {
		$host = $this->_config->hostname;
		$port = $this->_config->port;
		$user = $this->_config->username;
		$pass = $this->_config->password;
		$db   = $this->_config->dbname;

		$conn_str = "host='" . $host . "' "
		          . "port='" . $port . "' "
		          . "user='" . $user . "' "
				  . "password='" . $pass . "' "
				  . "dbname='" . $db . "' ";

		$this->_connection = pg_connect($conn_str);

		return is_resource($this->_connection);
	}

	public function close() {
		return pg_close($this->_connection);
	}

	public function query($sql) {
		$result = pg_query($this->_connection, $sql);

		if (is_resource($result)) {
			$this->_result = $result;
			return new Result($result);
		}

		return $result;
	}

	public function getAffectedRows() {
		return pg_affected_rows($this->_result);
	}

	public function getInsertId() {
		return pg_last_oid($this->_result);
	}

	public function getErrorNum() {
		return -1;
	}

	public function getErrorMsg() {
		return pg_last_error($this->_connection);
	}

	public function escapeString($str) {
		return pg_escape_string($this->_connection, $str);
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

<?php
/**
 * @package plutonium\database\mysql
 */

namespace Plutonium\Database\MySQL;

use Plutonium\Database\AbstractAdapter;

class Adapter extends AbstractAdapter {
	public function connect() {
		if (is_null($this->_connection)) {
			$host = $this->_config->hostname;
			$user = $this->_config->username;
			$pass = $this->_config->password;
			$db   = $this->_config->dbname;

			if ($this->_connection = @mysql_connect($host, $user, $pass))
				mysql_select_db($db, $this->_connection);
		}

		return is_resource($this->_connection);
	}

	public function close() {
		mysql_close($this->_connection);
	}

	public function query($sql) {
		$result = mysql_query($sql, $this->_connection);

		if (is_resource($result))
			return new Result($result);

		return $result;
	}

	public function getAffectedRows() {
		return mysql_affected_rows($this->_connection);
	}

	public function getInsertId() {
		return mysql_insert_id($this->_connection);
	}

	public function getErrorNum() {
		return mysql_errno($this->_connection);
	}

	public function getErrorMsg() {
		return mysql_error($this->_connection);
	}

	public function escapeString($str) {
		return mysql_real_escape_string($str, $this->_connection);
	}

	public function quoteString($str) {
		return "'" . $this->escapeString($str) . "'";
	}

	public function quoteSymbol($sym) {
		return "`" . $sym . "`";
	}

	public function stripString($str) {
		return trim($str, "'");
	}

	public function stripSymbol($sym) {
		return trim($sym, "`");
	}
}

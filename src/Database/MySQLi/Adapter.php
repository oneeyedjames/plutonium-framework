<?php
/**
 * @package plutonium\database\mysqli
 */

namespace Plutonium\Database\MySQLi;

use Plutonium\Database\AbstractAdapter;

class Adapter extends AbstractAdapter {
	public function connect() {
		if (is_null($this->_connection)) {
			$host = $this->_config->hostname;
			$user = $this->_config->username;
			$pass = $this->_config->password;
			$db   = $this->_config->dbname;

			$this->_connection = new \mysqli($host, $user, $pass, $db);

			if ($this->_connection->connect_errno) {
				$error = 'MySQL Error #' . $this->_connection->connect_errno
					. ': ' . $this->_connection->connect_error;

				trigger_error($error, E_USER_WARNING);

				return false;
			}
		}

		return true;
	}

	public function close() {
		return $this->_connection->close();
	}

	public function query($sql) {
		$result = $this->_connection->query($sql);

		if (is_bool($result)) return $result;

		return new Result($result);
	}

	public function getAffectedRows() {
		return $this->_connection->affected_rows;
	}

	public function getInsertId() {
		return $this->_connection->insert_id;
	}

	public function getErrorNum() {
		return $this->_connection->errno;
	}

	public function getErrorMsg() {
		return $this->_connection->error;
	}

	public function escapeString($str) {
		return $this->_connection->real_escape_string($str);
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

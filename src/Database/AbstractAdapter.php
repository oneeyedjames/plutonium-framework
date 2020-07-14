<?php
/**
 * @package plutonium\database
 */

namespace Plutonium\Database;

use Plutonium\Loader;

/**
 * Base class for vendor-specific database connections.
 */
abstract class AbstractAdapter {
	/**
	 * @ignore internal variable
	 */
	private static $_instance;

	/**
	 * Expected config args:
	 *   - driver: vendor name
	 *   - other constructor args
	 * Singleton method to initialize named concrete adapter class.
	 * @param object $config AccessObject
	 * @return object Global concrete Adapter object
	 */
	public static function getInstance($config = null) {
		if (is_null(self::$_instance) && !is_null($config)) {
			$type = '\\Plutonium\\Database\\' . $config->driver . '\\Adapter';
			if (Loader::import($type)) self::$_instance = new $type($config);
		}

		return self::$_instance;
	}

	/**
	 * @ignore internal variable
	 */
	protected $_config = null;

	/**
	 * @ignore internal variable
	 */
	protected $_connection = null;

	/**
	 * @param object $config AccessObject
	 */
	protected function __construct($config) {
		$this->_config = $config;

		if (!$this->connect())
			trigger_error("Unable to connect to database.", E_USER_ERROR);
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'driver':
				return $this->_config->driver;
		}
	}

	/**
	 * Opens a connection to the database server.
	 */
	abstract public function connect();

	/**
	 * Closes the connection to the database server.
	 */
	abstract public function close();

	/**
	 * Executes a database query and returns the result.
	 * @param string $sql Any valid SQL query
	 * @return object A concrete instance of AbstractResult
	 */
	abstract public function query($sql);

	/**
	 * Returns the number of rows affected by the most recent INSERT, UPDATE, or
	 * DELETE query on the current database connection.
	 *  @return integer Number of rows
	 */
	abstract public function getAffectedRows();

	/**
	 * Returns the most recently auto-generated primary key on the current
	 * database connection.
	 * @return mixed Record ID
	 */
	abstract public function getInsertId();

	/**
	 * Returns the most recent error number on the current database connection.
	 * @return integer Error number
	 */
	abstract public function getErrorNum();

	/**
	 * Returns the most recent error message on the current database connection.
	 * @return string Error message
	 */
	abstract public function getErrorMsg();

	/**
	 * Replaces reserved characers in a string with their vendor-specific
	 * escapes sequences.
	 * @param string $str Original text
	 * @return string Escaped text
	 */
	abstract public function escapeString($str);

	/**
	 * Wraps a string literal with vendor-specific quotes.
	 */
	abstract public function quoteString($str);

	/**
	 * Wraps a symbolic name with vendor-specific quotes.
	 */
	abstract public function quoteSymbol($sym);

	/**
	 * Removes vendor-specific quotes from a string literal.
	 */
	abstract public function stripString($str);

	/**
	 * Removes vendor-specific quotes from a symbolic name.
	 */
	abstract public function stripSymbol($sym);
}

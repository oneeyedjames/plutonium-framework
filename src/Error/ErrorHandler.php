<?php
/**
 * @package plutonium\error
 */

namespace Plutonium\Error;

/**
 * Reference implementation for runtime error handling.
 */
class ErrorHandler {
	/**
	 * @ignore internal variable
	 */
	protected static $_levels = null;

	/**
	 * @ignore internal variable
	 */
	protected static $_handlers = [];

	/**
	 * Registers an error handler for the specified level.
	 * @param mixed $handler Object or name of child class
	 * @param integer $level OPTIONAL Error level
	 */
	public final static function register($handler, $level = null) {
		if (is_null(self::$_levels)) {
			self::$_levels = E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE;
			set_error_handler([__CLASS__, 'handle'], self::$_levels);
		}

		if (is_null($level) || ($level & self::$_levels) == $level) {
			if (is_callable($handler))
				self::$_handlers[] = compact('handler', 'level');
			elseif (is_string($handler) && class_exists($handler))
				self::register(new $handler, $level);
		}
	}

	/**
	 * De-registers all error handlers.
	 */
	public final static function reset() {
		self::$_levels = null;
		self::$_handlers = [];
	}

	/**
	 * Dispatch method for runtime errors.
	 * @param integer $level Error level
	 * @param string $message Error message
	 * @param string $file Source file of error
	 * @param integer $line Line number of error
	 */
	public final static function handle($level, $message, $file, $line) {
		foreach (self::$_handlers as $meta) {
			if (is_null($meta['level']) || ($meta['level'] & $level) == $level) {
				if (call_user_func($meta['handler'], $level, $message, $file, $line)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @ignore magic method
	 */
	public final function __invoke($level, $message, $file, $line) {
		switch ($level) {
			case E_USER_ERROR:
				return $this->handleError($message, $file, $line);
			case E_USER_WARNING:
				return $this->handleWarning($message, $file, $line);
			case E_USER_NOTICE:
				return $this->handleNotice($message, $file, $line);
		}
	}

	/**
	 * Handler method for E_USER_ERROR level errors.
	 * @param string $message Error message
	 * @param string $file Source file of error
	 * @param integer $line Line number of error
	 * @return boolean True is error was handled, FALSE otherwise
	 */
	public function handleError($message, $file, $line) {
		return false;
	}

	/**
	 * handler method for E_USER_WARNING level errors.
	 * @param string $message Error message
	 * @param string $file Source file of error
	 * @param integer $line Line number of error
	 * @return boolean True is error was handled, FALSE otherwise
	 */
	public function handleWarning($message, $file, $line) {
		return false;
	}

	/**
	 * Handler method for E_USER_NOTICE level errors.
	 * @param string $message Error message
	 * @param string $file Source file of error
	 * @param integer $line Line number of error
	 * @return boolean True is error was handled, FALSE otherwise
	 */
	public function handleNotice($message, $file, $line)  {
		return false;
	}
}

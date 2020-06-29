<?php

namespace Plutonium\Error;

class ErrorHandler {
	protected static $_levels = null;
	protected static $_handlers = [];

	public static function register($handler, $level = null) {
		if (is_null(self::$_levels)) {
			self::$_levels = E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE;
			set_error_handler([__CLASS__, 'handle'], self::$_levels);
		}

		if (count(self::$_handlers)) return;

		if (is_null($level) || ($level & self::$_levels) == $level) {
			if (is_callable($handler))
				self::$_handlers[] = compact('handler', 'level');
			elseif (is_string($handler) && class_exists($handler))
				self::register(new $handler, $level);
		}
	}

	public static function reset() {
		self::$_levels = null;
		self::$_handlers = [];
	}

	public static function handle($level, $message, $file, $line) {
		foreach (self::$_handlers as $meta) {
			if (is_null($meta['level']) || ($meta['level'] & $level) == $level) {
				if (call_user_func($meta['handler'], $level, $message, $file, $line)) {
					return true;
				}
			}
		}

		return false;
	}

	public function __invoke($level, $message, $file, $line) {
		switch ($level) {
			case E_USER_ERROR:
				return $this->handleError($message, $file, $line);
			case E_USER_WARNING:
				return $this->handleWarning($message, $file, $line);
			case E_USER_NOTICE:
				return $this->handleNotice($message, $file, $line);
		}
	}

	public function handleError($message, $file, $line)   { return false; }
	public function handleWarning($message, $file, $line) { return false; }
	public function handleNotice($message, $file, $line)  { return false; }
}

<?php

namespace Plutonium\Error;

abstract class AbstractHandler {
	protected static $_levels   = null;
	protected static $_handlers = array();

	public static function register($handler, $level = null) {
		if (is_null(self::$_levels)) {
			self::$_levels = E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE;
			set_error_handler(array(__CLASS__, 'trigger'), self::$_levels);
		}

		if ($level == $level & self::$_levels || $level === null) {
			if (function_exists($handler)) {
				self::$_handlers[] = array (
					'level'   => $level,
					'handler' => $handler
				);
			} elseif (class_exists($handler)) {
				self::$_handlers[] = new $handler();
			}
		}
	}

	public static function trigger($level, $message) {
		foreach (self::$_handlers as $handler) {
			if (is_object($handler)) {
				if (method_exists($handler, 'handle')) {
					if ($handler->handle($level, $message))
						return true;
				}
			} elseif (is_array($handler)) {
				if ($handler['level'] == $level &&
					function_exists($handler['handler'])) {
					if (call_user_func($handler['handler'], $level, $message))
						return true;
				}
			}
		}

		return false;
	}

	public function handle($level, $message) {
		switch ($level) {
			case E_USER_ERROR:
				return $this->handleError($message);
			case E_USER_WARNING:
				return $this->handleWarning($message);
			case E_USER_NOTICE:
				return $this->handleNotice($message);
		}
	}

	abstract public function handleError($message);

	abstract public function handleWarning($message);

	abstract public function handleNotice($message);
}

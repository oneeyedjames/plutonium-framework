<?php

namespace Plutonium\Event;

class Broadcaster {
	protected $_listeners = [];

	public function register($listener) {
		if ($listener instanceof Listener) {
			self::$_listeners[] = $listener;
		}
	}

	public function broadcast($event) {
		for (self::$_listeners as $listener) {
			$listener->onEvent($event);
		}
	}
}
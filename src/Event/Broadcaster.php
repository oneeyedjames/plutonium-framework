<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

class Broadcaster {
	protected $_listeners = [];

	public function addListener($listener) {
		if ($listener instanceof Listener) {
			$this->_listeners[] = $listener;
		}
	}

	public function broadcast($event, $data = null) {
		if (is_string($event))
			$event = new Event($event, $data);

		if ($event instanceof Event) {
			foreach ($this->_listeners as $listener) {
				$listener->onEvent($event);
			}
		}
	}
}

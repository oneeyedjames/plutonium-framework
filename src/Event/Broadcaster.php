<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

/**
 * Reference implementation of pub/sub notification pattern.
 */
class Broadcaster {
	/**
	 * @ignore internal variable
	 */
	protected $_listeners = [];

	/**
	 * Registers an instance of an even listener.
	 * @param object $listener Object implementing Listener interface
	 */
	public function addListener($listener) {
		if ($listener instanceof Listener) {
			$this->_listeners[] = $listener;
		}
	}

	/**
	 * Broadcasts an event to all registered listeners.
	 * @param mixed $event Event object or string name
	 * @param mixed $data OPTIONAL User-defined data
	 */
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

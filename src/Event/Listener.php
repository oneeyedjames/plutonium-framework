<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

interface Listener {
	/**
	 * Event callback method.
	 * @param object $event Event object
	 */
	function onEvent($event);
}

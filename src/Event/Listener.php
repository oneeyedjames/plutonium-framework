<?php
/**
 * @package plutonium\event
 */

namespace Plutonium\Event;

interface Listener {
	function onEvent($event);
}

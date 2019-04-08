<?php

namespace Plutonium\Event;

interface Listener {
	function onEvent($event);
}
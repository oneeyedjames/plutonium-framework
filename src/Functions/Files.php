<?php

namespace Plutonium\Functions;

function filepath($path) {
	if (preg_match('/^([a-z]+:\/\/)(.*)/i', $path, $match)) {
		$scheme = $match[1];
		$path = $match[2];
	} else {
		$scheme = '';
	}

	$path = str_replace([FS, BS], DS, $path);

	$absolute = $path[0] == DS;

	if ($path[0] == '~') {
		$path = getenv('HOME') . DS . substr($path, 1);
	} elseif (!$absolute && !$scheme) {
		$path = getcwd() . DS . $path;
	}

	$oldparts = explode(DS, $path);
	$newparts = [];

	foreach ($oldparts as $part) {
		if ($part == '.' || $part == '') continue;
		elseif ($part == '..') array_pop($newparts);
		else $newparts[] = $part;
	}

	$path = implode(DS, $newparts);

	if ($absolute || !$scheme)
		$path = DS . $path;

	return $scheme . $path;
}
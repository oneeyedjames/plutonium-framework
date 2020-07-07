<?php
/**
 * @package plutonium\functions\file
 */

namespace Plutonium\Functions;

function filepath($path) {
	if (preg_match('/^([a-z]+:\/\/)(.*)/i', $path, $match)) {
		$scheme = $match[1];
		$path = $match[2];
	} else {
		$scheme = '';
	}

	$absolute = in_array($path[0], [FS, BS]);

	if ($path[0] == '~')
		$path = getenv('HOME') . DS . $path;
	elseif (!$absolute && !$scheme)
		$path = getcwd() . DS . $path;

	$oldparts = splitpath($path);
	$newparts = [];

	foreach ($oldparts as $part) {
		if (in_array($part, ['.', '~', ''])) continue;
		elseif ($part == '..') array_pop($newparts);
		else $newparts[] = $part;
	}

	$path = joinpath($newparts);

	if ($absolute || !$scheme)
		$path = DS . $path;

	return $scheme . $path;
}

function joinpath($parts) {
	$parts = is_array($parts) ? $parts : func_get_args();
	return implode(DS, $parts);
}

function splitpath($path) {
	return explode(DS, cleanpath($path));
}

function cleanpath($path) {
	return trim(str_replace([FS, BS], DS, $path), DS);
}

<?php
/**
 * @package plutonium\functions\file
 */

namespace Plutonium\Functions;

/**
 * Normalizes a file path, using the host system's directory separator and
 * preserving URI schemes. Wildcards are converted into their canonical
 * representations and relative paths are converted into absolute paths within
 * the current working directory.
 * @param string $path File path
 * @return string Normalized absolute file path
 */
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

/**
 * Concatenates an array of node names into a file path, using the host system's
 * directory separator. Returned path will not contain leading/trailing
 * separators.
 * @param array $parts Array of node names
 * @return string Normalized file path
 */
function joinpath($parts) {
	$parts = is_array($parts) ? $parts : func_get_args();
	return implode(DS, $parts);
}

/**
 * Splits a file path into an array of individual node names.
 * @param string $path File path
 * @return array Array of node names
 */
function splitpath($path) {
	return explode(DS, cleanpath($path));
}

/**
 * Normalizes a file path, using the host system's directory separator and
 * removing leading/trailing separators.
 * @param string $path File path
 * @return string Normalized file path
 */
function cleanpath($path) {
	return trim(str_replace([FS, BS], DS, $path), DS);
}

<?php

namespace Plutonium\Functions;

function paragraphize($text) {
	$html = str_replace("\r\n", "\n", $text);
	$html = str_replace("\r", "\n", $html);
	$html = str_replace("\n\n", "</p><p>", $html);
	$html = str_replace("\n", "<br>\n", $html);
	$html = str_replace("</p><p>", "</p>\n<p>", $html);
	$html = "<p>{$html}</p>";

	return $html;
}

function slugify($string) {
	$pattern = array('/\s+/', '/[^0-9a-z-]/i');
	$replace = array('-', '');

	return strtolower(preg_replace($pattern, $replace, $string));
}

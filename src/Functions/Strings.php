<?php
/**
 * @package plutonium\functions\string
 */

namespace Plutonium\Functions;

/**
 * Converts plain text into HTML markup. Line breaks are replaced with
 * &lt;br&gt; tags and blocks of text separated by empty lines are wrapped in
 * &lt;p&gt;&lt;/p&gt; tags.
 * @param string $text Plain text
 * @return string HTML markup
 */
function paragraphize($text) {
	$html = str_replace("\r\n", "\n", $text);
	$html = str_replace("\r", "\n", $html);
	$html = str_replace("\n\n", "</p><p>", $html);
	$html = str_replace("\n", "<br>\n", $html);
	$html = str_replace("</p><p>", "</p>\n<p>", $html);
	$html = "<p>{$html}</p>";

	return $html;
}

/**
 * Converts plain text into a URL-safe string. Non-alphanumeric characters are
 * removed, alpha characters are lower-cased, and all whitespace is replaced by
 * hyphens.
 * @param string $string Plain text
 * @return string URL-safe string
 */
function slugify($string) {
	$pattern = array('/\s+/', '/[^0-9a-z-]/i');
	$replace = array('-', '');

	return strtolower(preg_replace($pattern, $replace, $string));
}

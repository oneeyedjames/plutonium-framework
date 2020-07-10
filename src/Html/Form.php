<?php
/**
 * @package plutonium\html
 */

namespace Plutonium\Html;

class Form extends Tag {
	/**
	 * @param array $attributes OPTIONAL Tag attributes as key-value pairs
	 * @param array $child_tags OPTIONAL Array of Tag objects
	 */
	public function __construct($attributes = array(), $child_tags = array()) {
		parent::__construct('form', $attributes, $child_tags, false);
	}
}

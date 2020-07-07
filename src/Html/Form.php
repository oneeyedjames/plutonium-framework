<?php
/**
 * @package plutonium\html
 */

namespace Plutonium\Html;

class Form extends Tag {
    public function __construct($attributes = array(), $child_tags = array()) {
        parent::__construct('form', $attributes, $child_tags, false);
    }
}

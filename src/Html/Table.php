<?php
/**
 * @package plutonium\html
 */

namespace Plutonium\Html;

class Table extends Tag {
    public function __construct($attributes = array(), $child_tags = array()) {
        parent::__construct('table', $attributes, $child_tags, false);
    }
}

<?php
/**
 * @package plutonium\html
 */

namespace Plutonium\Html;

/**
 * Base class for all HTML tags
 * @property string $name HTML tag name
 * @property array $attributes Tag attributes as key-value pairs
 */
class Tag {
	/**
	 * @ignore internal variable
	 */
	protected $_name;

	/**
	 * @ignore internal variable
	 */
	protected $_attributes;

	/**
	 * @ignore internal variable
	 */
	protected $_child_tags;

	/**
	 * @ignore internal variable
	 */
	protected $_self_close;

	/**
	 * @param string $name HTML tag name
	 * @param array $attributes OPTIONAL Tag attributes as key-value pairs
	 * @param array $child_tags OPTIONAL Array of Tag objects
	 * @param boolean $self_close OPTIONAL Whether to self-close body-less tag
	 */
	public function __construct($name, $attributes = array(), $child_tags = array(), $self_close = true) {
		$this->_name = $name;
		$this->_attributes = is_array($attributes) ? $attributes : array();
		$this->_child_tags = is_array($child_tags) ? $child_tags : array();
		$this->_self_close = $self_close;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'attributes':
				return $this->_attributes;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __set($key, $value) {
		switch ($key) {
			case 'name':
				$this->_name = $value;
				break;
			case 'attributes':
				$this->setAttributes($values);
				break;
			default:
				$this->_attributes[$key] = $value;
				break;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __isset($key) {
		switch ($key) {
			case 'name':
			case 'attributes':
				return true;
			default:
				return isset($this->_attributes[$key]);
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __unset($key) {
		switch ($key) {
			case 'name':
			case 'attributes':
				break;
			default:
				if (isset($this->_attributes[$key]))
					unset($this->_attributes[$key]);
				break;
		}
	}

	/**
	 * @ignore magic method
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Adds another Tag object to be nested inside this one.
	 * @param object $tag Tag object
	 */
	public function addChildTag($tag) {
		$this->_child_tags[] = $tag;
	}

	/**
	 * Sets self-close flag on tag.
	 * @param boolean $self_close Whether to self-close body-less tag
	 */
	public function setSelfClose($self_close) {
		$this->_self_close = $self_close;
	}

	/**
	 * Formats the tag as s HTML string.
	 * @return string Formatted HTML
	 */
	public function toString() {
		$html = '<' . strtolower($this->_name);

		foreach ($this->_attributes as $key => $value)
			$html .= ' ' . $key . '="' . $value . '"';

		$inner_html = '';

		foreach ($this->_child_tags as $tag)
			$inner_html .= $tag->toString();

		if (empty($inner_html) && $this->_self_close) {
			$html .= ' />';
		} else {
			$html .= '>' . $this->_inner_html
			      .  '</' . strtolower($this->_name) . '>';
		}

		return $html;
	}
}

<?php

namespace Plutonium\Filter;

class StringFilter extends AbstractFilter {
	public function alphaFilter($value) {
		return preg_replace('/[^A-Z]/i', '', $value);
	}

	public function alnumFilter($value) {
		return preg_replace('/[^A-Z0-9]/i', '', $value);
	}

	public function digitFilter($value) {
		return preg_replace('/[^0-9]/', '', $value);
	}

	public function hexitFilter($value) {
		return preg_replace('/[^A-F0-9]/i', '', $value);
	}

	public function lcaseFilter($value) {
		return preg_replace('/[^a-z]/', '', $value);
	}

	public function ucaseFilter($value) {
		return preg_replace('/[^A-Z]/', '', $value);
	}
}

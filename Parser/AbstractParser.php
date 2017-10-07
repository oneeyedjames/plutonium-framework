<?php

namespace Plutonium\Parser;

abstract class AbstractParser {
	protected $_namespace = 'pu';

	protected $_application = null;

	public function __construct($application) {
		$this->_application = $application;
	}

	public function parse($tmpl) {
		$pattern = '/<(?<ns>\w+):(?<tag>\w+)\s?(?<args>[^<>]*)>/msi';
		$matches = array();

		$subpattern = '/(?<key>\w+)="(?<value>[^"]*)"/msi';
		$submatches = array();

		$html = $tmpl;

		if ($count = preg_match_all($pattern, $tmpl, $matches)) {
			for ($i = 0; $i < $count; $i++) {
				$tag = array(
					'ns'   => $matches['ns'][$i],
					'tag'  => $matches['tag'][$i],
					'args' => array()
				);

				if ($subcount = preg_match_all($subpattern, $matches['args'][$i], $submatches)) {
					for ($j = 0; $j < $subcount; $j++) {
						$tag['args'][$submatches['key'][$j]] = $submatches['value'][$j];
					}
				}

				$data = $this->process($tag['ns'], $tag['tag'], $tag['args']);

				if ($data !== false) {
					$stub = preg_quote($matches[0][$i]);
					$html = preg_replace('|' . $stub . '|', $data, $html, 1);
				}
			}
		}

		return $html;
	}

	public function process($ns, $tag, $args) {
		if ($ns == $this->_namespace) {
			$method = strtolower($tag) . 'Tag';

			if (method_exists($this, $method))
				return call_user_func(array($this, $method), $args);
		}

		return false;
	}
}

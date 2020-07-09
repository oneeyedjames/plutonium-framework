<?php
/**
 * @package plutonium\parser
 */

namespace Plutonium\Parser;

/**
 * Base class for all template parsers.
 * Child classes  must provide a method for each supported tag. For instance, a
 * tag named &lt;pu:date&gt; must provide a method with signature dateTag($args).
 * @property-read string $namespace Namespace for template tags
 * @property-read object $application The active Appplication object
 */
abstract class AbstractParser {
	/**
	 * Default namespace.
	 * Child classes may override this value to encapsulate tags into a custom
	 * namespace.
	 */
	protected $_namespace = 'pu';

	/**
	 * @ignore internal variable
	 */
	protected $_application = null;

	/**
	 * @param object $application The active Application object
	 */
	public function __construct($application) {
		$this->_application = $application;
	}

	/**
	 * @ignore magic method
	 */
	public function __get($key) {
		switch ($key) {
			case 'namespace':
			case 'application':
				return $this->{"_$key"};
		}
	}

	/**
	 * Parses a template and replaces each matched template tag with content.
	 * @param string $tmpl Raw template body
	 * @return string Parsed template body with content
	 */
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

	/**
	 * Dispatches tag name and attributes to matching handler method.
	 * @param string $ns Tag namespace
	 * @param string $tag Tag name
	 * @param array $args Tag attributes
	 * @return string Final content, FALSE if no handler matches tag
	 */
	public function process($ns, $tag, $args) {
		if ($ns == $this->_namespace) {
			$method = strtolower($tag) . 'Tag';

			if (method_exists($this, $method))
				return call_user_func(array($this, $method), $args);
		}

		return false;
	}
}

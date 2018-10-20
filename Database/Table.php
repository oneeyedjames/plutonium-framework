<?php

namespace Plutonium\Database;

use Plutonium\Object;
use Plutonium\Application\Application;
use Plutonium\Application\Module;

use DOMDocument;
use DOMXPath;

class Table {
	protected static $_tables = array();
	protected static $_xref_tables = array();
	protected static $_refs = array();

	protected static function buildXRefTable($node, $xpath, &$cfg) {
		$name   = $node->getAttribute('name');
		$alias  = $node->getAttribute('alias');
		$table  = $node->getAttribute('table');
		$prefix = $node->getAttribute('prefix');

		if (empty($name))   $name   = $table;
		if (empty($alias))  $alias  = $cfg->name;
		if (empty($prefix)) $prefix = $cfg->prefix;

		$xref_cfg = new Object(array(
			'driver'     => $cfg->driver,
			'prefix'     => $cfg->prefix,
			'suffix'     => 'xref',
			'name'       => $alias . '_' . $name,
			'timestamps' => $node->getAttribute('timestamps'),
			'refs'       => array(
				new Object(array(
					'name'   => $alias,
					'table'  => $cfg->name,
					'prefix' => $cfg->prefix
				)),
				new Object(array(
					'name'   => $name,
					'table'  => $table,
					'prefix' => $prefix
				))
			)
		));

		$fields = array();

		$subnodes = $xpath->query('field', $node);
		foreach ($subnodes as $subnode) {
			$fields[] = new Object(array(
				'name' => $subnode->getAttribute('name'),
				'type' => $subnode->getAttribute('type'),
				'size' => $subnode->getAttribute('size')
			));
		}

		$xref_cfg->fields = $fields;

		$xref_table = new self($xref_cfg);

		self::$_xref_tables[$table][$alias] =& $xref_table;
		self::$_xref_tables[$cfg->name][$name] =& $xref_table;

		return $xref_table;
	}

	public static function getInstance($name, $module = null) {
		if (!isset(self::$_tables[$name])) {
			$name = strtolower($name);
			$type = ucfirst($name) . 'Table';

			if (is_null($module))
				$path = Application::getPath();
			else
				$path = Module::getPath() . DS . strtolower($module);

			$file = $path . DS . 'models' . DS . $name . '.xml';

			if (is_file($file)) {
				$cfg = new Object();
				$cfg->driver = AbstractAdapter::getInstance()->driver;

				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = true;
				$doc->formatOutput = true;
				$doc->load($file);

				$xpath = new DOMXPath($doc);

				$table = $xpath->query('/table')->item(0);

				$cfg->name       = $table->getAttribute('name');
				$cfg->prefix     = $table->getAttribute('prefix');
				$cfg->timestamps = $table->getAttribute('timestamps');

				if (!is_null($module)) {
					$cfg->prefix = 'mod';
					$cfg->module = $module;
				}

				$fields = array();

				$nodes = $xpath->query('/table/field');
				foreach ($nodes as $field) {
					$fields[] = new Object(array(
						'name'   => $field->getAttribute('name'),
						'type'   => $field->getAttribute('type'),
						'size'   => $field->getAttribute('size'),
						'length' => $field->getAttribute('length'),
					));
				}

				$cfg->fields = $fields;

				$refs = array();

				$nodes = $xpath->query('/table/ref');
				foreach ($nodes as $node) {
					$ref = new Object(array(
						'name'   => $node->getAttribute('name'),
						'table'  => $node->getAttribute('table'),
						'prefix' => $node->getAttribute('prefix')
					));

					$refs[] = $ref;

					self::$_refs[$ref->table][$ref->alias] = new Object(array(
						'table' => $cfg->name,
						'alias' => $ref->name
					));
				}

				$cfg->refs = $refs;

				//$xrefs = array();

				$nodes = $xpath->query('/table/xref');
				foreach ($nodes as $node)
					$xref_table = self::buildXRefTable($node, $xpath, $cfg);

				//$cfg->xrefs = $xrefs;

				self::$_tables[$name] = new self($cfg);
			}
		}

		return @self::$_tables[$name];
	}

	public static function getRefs($table) {
		if (array_key_exists($table, self::$_refs)) {
			$refs = self::$_refs[$table];

			unset(self::$_refs[$table]);

			return $refs;
		}

		return array();
	}

	public static function getXRefs($table) {
		if (array_key_exists($table, self::$_xref_tables)) {
			return self::$_xref_tables[$table];

			/* $xrefs = self::$_xref_tables[$table];

			unset(self::$_xref_tables[$table]);

			return $xrefs; */
		}

		return array();
	}

	protected $_delegate = null;

	protected $_name   = null;
	protected $_prefix = null;
	protected $_suffix = null;
	protected $_module = null;

	protected $_table_name = null;
	protected $_table_meta = array();
	protected $_field_meta = array();

	protected $_table_refs  = array();
	protected $_table_revs  = array();
	protected $_table_xrefs = array();

	public function __construct($config) {
		$type = 'Plutonium\\Database\\' . $config->driver . '\\Delegate';

		$this->_delegate = new $type($this);

		$this->_name   = $config->name;
		$this->_prefix = $config->prefix;
		$this->_suffix = $config->suffix;

		$table_name = array($config->prefix);

		if ($config->prefix == 'mod')
			$table_name[] = $this->_module = $config->module;

		$table_name[] = $config->name;

		if (isset($config->suffix))
			$table_name[] = $config->suffix;

		$this->_table_name = implode('_', $table_name);

		$this->_table_meta = new Object(array(
			'timestamps' => $config->timestamps == 'yes'
		));

		if ($config->suffix != 'xref') {
			$this->_field_meta['id'] = new Object(array(
				'name'     => 'id',
				'type'     => 'int',
				'null'     => false,
				'auto'     => true,
				'unsigned' => true
			));
		}

		foreach ($config->refs as $ref) {
			$this->_table_refs[$ref->name] = $ref->table;

			$field_name = $ref->name . '_id';

			$this->_field_meta[$field_name] = new Object(array(
				'name'     => $field_name,
				'type'     => 'int',
				'null'     => false,
				'unsigned' => true,
				'default'  => 0,
				'index'    => true
			));
		}

		if ($config->timestamps == 'yes') {
			$this->_field_meta['created'] = new Object(array(
				'name'    => 'created',
				'type'    => 'date',
				'null'    => true
			));

			$this->_field_meta['updated'] = new Object(array(
				'name'    => 'updated',
				'type'    => 'date',
				'null'    => true
			));
		}

		foreach ($config->fields as $field) {
			$this->_field_meta[$field->name] = new Object(array(
				'name'     => $field->name,
				'type'     => $field->type,
				'size'     => $field->size,
				'length'   => $field->length,
				'null'     => $field->null != 'no',
				'unsigned' => $field->unsigned == 'yes',
				'default'  => $field->default
			));
		}
	}

	public function __get($key) {
		switch ($key) {
			case 'name':
				return $this->_name;
			case 'table_name':
				return $this->_table_name;
			case 'table_meta':
				return $this->_table_meta;
			case 'field_names':
				return array_keys($this->_field_meta);
			case 'field_meta':
				return $this->_field_meta;
			case 'table_refs':
				return $this->_table_refs;
			case 'table_revs':
				if (empty($this->_table_revs)) {
					$this->_table_revs = self::getRefs($this->_name);

					foreach ($this->_table_revs as &$rev) {
						$table = self::getInstance($rev->table);

						if ($table->_prefix == 'mod')
							$rev->alias = $table->_module . '_' . $rev->alias;
					}
				}

				return $this->_table_revs;
			case 'table_xrefs':
				if (empty($this->_table_xrefs))
					$this->_table_xrefs = self::getXRefs($this->_name);

				return $this->_table_xrefs;
		}
	}

	public function create() {
		if (!$this->_delegate->exists() && !$this->_delegate->create()) {
			$message = self::getInstance()->getErrorMsg();
			trigger_error($message, E_USER_ERROR);
		}

		foreach (self::getXRefs($this->name) as $xref) $xref->create();
	}

	public function make($data = null, $xref_data = null) {
		return new Row($this, $data, $xref_data);
	}

	public function find($args = null, $sort = null, $limit = 0, $offset = 0) {
		return $this->_delegate->select($args, $sort, $limit, $offset);
	}

	public function find_xref($xref, $args = null) {
		return $this->_delegate->select_xref($this->table_xrefs[$xref], $args);
	}

	public function save($row) {
		return is_null($row->id)
			 ? $this->_delegate->insert($row)
			 : $this->_delegate->update($row);
	}

	public function delete($id) {
		return $this->_delegate->delete($id);
	}
}

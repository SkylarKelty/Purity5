<?php
/**
 * Purity5 is a HTML5 parser
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace SkylarK\Purity5\DataStructures;

class PureTree
{
	/** The name of this node */
	private $_name;
	/** Attributes of this node */
	private $_attributes;
	/** The contents of this node */
	private $_contents;
	/** Children of this node */
	private $_children;
	/** Node's path in the doc */
	private $_path;

	/**
	 * Construct a new tree
	 * 
	 * @param string $path The overall path of this element in the doc root
	 * @param string $name The name of this element
	 * @param string $contents The HTML contents of this element
	 * @param array  $attributes A list of our attributes
	 */
	private function __construct($path, $name, $contents = '', $attributes = array()) {
		$this->_name = $name;
		$this->_attributes = $attributes;
		$this->_contents = $contents;
		$this->_children = array();
		$this->_path = $path;
		$this->_path[] = $name;
	}

	/**
	 * Build a root element
	 */
	public static function buildRoot($contents) {
		return new PureTree(array(), "html", $contents);
	}

	/**
	 * Return the name of this element
	 */
	public function name() {
		return $this->_name;
	}

	/**
	 * Return the attributes of this element
	 */
	public function attributes() {
		return $this->_attributes;
	}

	/**
	 * Return the contents of this element
	 */
	public function contents() {
		return $this->_contents;
	}

	/**
	 * Returns our children
	 */
	public function children() {
		return $this->_children;
	}

	/**
	 * Returns our path
	 */
	public function path() {
		return $this->_path;
	}

	/**
	 * Create a new child
	 * 
	 * @param string $name The name of this element
	 * @param array  $attributes A list of our attributes
	 * @param string $contents The HTML contents of this element
	 *
	 * @return PureTree The resulting PureTree object
	 */
	public function createChild($name, $attributes = array(), $contents = '') {
		$child = new PureTree($this->_path, $name, $contents, $attributes);
		$this->_children[] = $child;
		return $child;
	}

	/**
	 * Recursive query function
	 *
	 * @param string $query The query to validate against
	 */
	public function query($query) {
		return $this->_query(new Query($query));
	}

	/**
	 * Recursive query function (internal version)
	 *
	 * @param Query $query The query to validate against
	 */
	private function _query(Query $query) {
		$result = array();
		if ($query->match($this)) {
			$result[] = $this;
		}
		foreach ($this->_children as $child) {
			$result = array_merge($result, $child->_query($query));
		}
		return $result;
	}
}
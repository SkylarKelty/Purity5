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

	/**
	 * Construct a new tree
	 * 
	 * @param string $name The name of this element
	 * @param array $attributes A list of our attributes
	 * @param string $contents the HTML contents of this element
	 */
	public function __construct($name, $attributes, $contents) {
		$this->_name = $name;
		$this->_attributes = $attributes;
		$this->_contents = $contents;
		$this->_children = array();
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
	 * Add a new child
	 * 
	 * @param PureTree $child The child to add
	 */
	public function addChild(PureTree $child) {
		$this->_children[] = $child;
	}

	/**
	 * Recursive query function
	 *
	 * @param string $query The query to validate against
	 */
	public function query($query) {
		return $this->_query(new Query($string));
	}

	/**
	 * Recursive query function (internal version)
	 *
	 * @param Query $query The query to validate against
	 */
	private function _query(Query $query) {
		$result = array();
		if ($query->match($this)) {
			$result[] = $this->name;
		}
		foreach ($this->children as $child) {
			$result = array_merge($result, $child->_query($query, ''));
		}
		return $result;
	}
}
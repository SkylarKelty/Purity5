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

	/**
	 * Construct a new tree
	 */
	public function __construct($name, $attributes) {
		$this->_name = $name;
		$this->_attributes = $attributes;
	}
}
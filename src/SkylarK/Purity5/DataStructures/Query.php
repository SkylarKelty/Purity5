<?php
/**
 * Purity5 is a HTML5 parser
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace SkylarK\Purity5\DataStructures;

/**
 * A Query is used to breakup a string into a set of rules and match 
 * PureTrees against those rules
 */
class Query
{
	/** Stored raw string */
	private $_query;

	/**
	 * Construct a new Query
	 * 
	 * @param string $query The string form query (e.g. "html > body > h1")
	 */
	public function __construct($query) {
		$this->_query = $query;
		$this->prepare();
	}

	/**
	 * Prepare this object for matching
	 */
	private function prepare() {
		// TODO
	}

	/**
	 * Match an object against this query
	 * 
	 * @param  PureTree $tree The tree to match
	 * @param  string   $path The path of the object
	 * @return boolean        The result (true if there was a match)
	 */
	public function match(PureTree $tree, $path) {
		$name = $tree->name();
		$attributes = $tree->attributes();

		return $this->_query == $name || $this->_query == $path;
	}
}
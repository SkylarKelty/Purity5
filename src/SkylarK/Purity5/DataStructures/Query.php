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
	/** The path this query takes */
	private $_query_path;

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
		$this->_query_path = self::breakPath($this->_query);
	}

	/**
	 * Breaks up a query path, returning an array of the elements this leaks down into
	 */
	public static function breakPath($query) {
		$result = array();

		// Breakup
		$buffer = '';
		$len = strlen($query);
		for ($i = 0; $i < $len; $i++) {
			$chr = $query[$i];
			// Split by > and whitespace
			if ($chr == '>' || $chr == '+' || $chr == ' ') {
				if ($buffer !== '') {
					$result[] = $buffer;
					$buffer = '';
				}
				if ($chr != ' ') {
					$result[] = $chr;
				}
				continue;
			}
			$buffer .= $chr;
		}

		// Store whatevers left in the buffer (if there is anything there)
		if ($buffer !== '') {
			$result[] = $buffer;
		}

		return $result;
	}

	/**
	 * Match two elements
	 */
	private function match($tree, $query) {
		return $tree->name() == $query;
	}

	/**
	 * Search a tree for a given element
	 */
	private function search($tree, $query) {
		$results = array();
		if ($this->match($tree, $query)) {
			$results[] = $tree;
		}
		foreach ($tree->children() as $child) {
			$results = array_merge($results, $this->search($child, $query));
		}
		return $results;
	}

	/**
	 * Internal run method
	 */
	private function _run($tree, $path) {
		$resultSet = array();
		
		// Search the tree for everything that matches the first element
		$searchSet = $this->search($tree, array_shift($path));
		/*if (empty($searchSet)) {
			return false;
		}

		// If we have no path left, then we won
		if (empty($path)) {
			return $searchSet;
		}

		// Okay we have a start
		while (!empty($path)) {
			$elem = array_shift($path);
			switch ($elem) {
				case '+':
					break;
				case '>':
					break;
				default:
					// Search the search sets
					$results = array();
					foreach ($searchSet as $branch) {
						$results = array_merge($results, $this->search($branch, $elem));
					}
					$searchSet = $results;
			}
		}*/

		return $searchSet;
	}

	/**
	 * Run this query against an object
	 * 
	 * @param  PureTree $tree The tree to match
	 * @return array The result set
	 */
	public function run(PureTree $tree) {
		return $this->_run($tree, $this->_query_path);
	}
}
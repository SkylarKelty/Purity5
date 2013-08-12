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
		$name = $tree->name();
		$attrs = $tree->attributes();

		// Our easiest selector
		if ($query == '*' || $name == $query) {
			return true;
		}

		// Do we have a colon selector?
		if (strpos($query, ":") !== false) {
			list($query, $selector) = explode(":", $query);

			// Do we have a bracket?
			if (strpos($selector, "(") !== false) {
				list($selector, $value) = explode("(", $selector);
				$value = substr($value, 0, -1);
			}

			// Only return false here, follow through the rest of the selectors
			switch ($selector) {
				case "first-child":
					// Check we are the first child
					$parent = $tree->parent();
					if ($parent) {
						$children = $parent->children();
						if ($children[0] != $tree) {
							return false;
						}
					}
					break;
				case "last-child":
					// Check we are the first child
					$parent = $tree->parent();
					if ($parent) {
						$children = $parent->children();
						if (end($children) != $tree) {
							return false;
						}
					}
					break;
				case "nth-child":
					// Check we are the first child
					$parent = $tree->parent();
					if ($parent) {
						$children = $parent->children();
						$key = array_search($tree, $children, true);
						switch ($value) {
							case "even":
								if ($key % 2 != 0) {
									return false;
								}
								break;
							case "odd":
								if ($key % 2 == 0) {
									return false;
								}
								break;
							default:
								if ($key != $value) {
									return false;
								}
						}
					}
					break;
				default:
					// Invalid selector
					throw new \Exception("Invalid selector: " . $selector);
					break;
			}
		}

		// Do we have a #?
		if (strpos($query, "#") !== false) {
			list($elem, $id) = explode("#", $query);

			if (!isset($attrs['id']) || (!empty($elem) && $name != $elem)) {
				return false;
			}

			return $id == $attrs['id'];
		}

		// Do we have a .?
		if (strpos($query, ".") !== false) {
			$arr = explode(".", $query);
			$elem = array_shift($arr);

			if (!isset($attrs['class']) || (!empty($elem) && $name != $elem)) {
				return false;
			}

			$classes = explode(" ", $attrs['class']);
			foreach ($arr as $class) {
				if (!in_array($class, $classes)) {
					return false;
				}
			}
			return true;
		}

		// Here again in case the query was modified
		return $query == '*' || $name == $query;
	}

	/**
	 * Match two elements in "plusMode"
	 */
	private function plusModeMatch($tree, $query, $matchElem) {
		if (!$this->match($tree, $query)) {
			return false;
		}
		// We also want to check if the previous element of our parent was $matchElem
		$parent = $tree->parent();
		$prev = null;
		foreach ($parent->children() as $child) {
			if ($prev !== null && $this->match($prev, $matchElem) && $child == $tree) {
				return true;
			}
			$prev = $child;
		}
		return false;
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

		$matchMode = 0;
		$len = count($path);
		for ($i = 0; $i < $len; $i++) {
			$query = $path[$i];

			// Look ahead, do we have a + coming up?
			// If so, discount this tag
			if (isset($path[$i + 1]) && $path[$i + 1] == '+') {
				if ($i == 0) {
					// The query looks roughly like this: "a + b"
					// This is a special case, lets find the parents of all of the 'b's
					$searchSet = $this->search($tree, $path[$i + 2]);
					foreach ($searchSet as $elem) {
						$parent = $elem->parent();
						if (!in_array($parent, $resultSet)) {
							$resultSet[] = $parent;
						}
					}
				}
				continue;
			}

			// Process this query
			switch ($query) {
				case '>':
					$matchMode = 1;
					break;
				case '+':
					$matchMode = 2;
					break;
				default:
					// Special case for 0
					if ($i == 0) {
						$resultSet = $this->search($tree, $path[0]);
						continue;
					}

					// Search the current result set
					$searchSet = $resultSet;
					$resultSet = array();
					foreach ($searchSet as $elem) {
						foreach ($elem->children() as $child) {
							if ($matchMode == 0) {
								$results = $this->search($child, $query);
								foreach ($results as $result) {
									if (!in_array($result, $resultSet)) {
										$resultSet[] = $result;
									}
								}
							}
							if ($matchMode == 1 && $this->match($child, $query)) {
								$resultSet[] = $child;
							}
							if ($matchMode == 2 && $this->plusModeMatch($child, $query, $path[$i - 2])) {
								$resultSet[] = $child;
							}
						}
					}
					$matchMode = 0;
			}
		}

		return $resultSet;
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
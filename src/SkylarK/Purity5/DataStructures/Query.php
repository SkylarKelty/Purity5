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
	 * Match two paths, if the first path appears anywhere in the second path
	 */
	protected function matchPath($path, $directory) {
		$matchDirectory = $directory;
		$depth = 0;
		foreach ($path as $elem) {
			if ($elem == '>') {
				$depth = 1;
				continue;
			}
			if (empty($matchDirectory)) {
				return false;
			}

			// If we are only looking at depth 1, we can bail out early
			if ($depth == 1) {
				if ($matchDirectory[0] != $elem) {
					return false;
				}
				$depth = 0;
				$matchDirectory = array_slice($matchDirectory, 1);
				continue;
			}

			// Go through $matchDirectory and see if our element matches
			$match = array_search($elem, $matchDirectory);
			if ($match === false) {
				return false;
			}

			// Cut down the matchDirectory
			$matchDirectory = array_slice($matchDirectory, $match + 1);
		}
		return count($matchDirectory) === 0;
	}

	/**
	 * Match an object against this query
	 * 
	 * @param  PureTree $tree The tree to match
	 * 
	 * @return boolean        The result (true if there was a match)
	 */
	public function match(PureTree $tree) {
		$name = $tree->name();
		$attributes = $tree->attributes();
		$path = $tree->path();
		return $this->_query == $name || $this->_query_path == $path || $this->matchPath($this->_query_path, $path);
	}
}
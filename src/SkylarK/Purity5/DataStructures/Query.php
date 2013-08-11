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
	protected function matchPath($tree) {
		$path = $this->_query_path;
		$matchDirectory = $tree->path();

		$depth = 0;
		$ignoreCount = 0;
		foreach ($path as $i => $elem) {
			// Are we currently ignoring elements?
			if ($ignoreCount > 0) {
				$ignoreCount--;
				continue;
			}

			// Setting depth?
			if ($elem == '>') {
				$depth = 1;
				continue;
			}

			// Well... this didnt work.
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

			// Look ahead, is there a plus?
			$plusMatch = false;
			if (isset($path[$i + 1]) && $path[$i + 1] == '+') {
				// Expect another
				if (!isset($path[$i + 2])) {
					throw new Exception("Invalid query, + encountered with no counter element");
				}
				$plusMatch = $elem;
				$elem = $path[$i + 2];
				$ignoreCount = 2;
			}

			// Go through $matchDirectory and see if our element matches
			$match = array_search($elem, $matchDirectory);
			if ($match === false) {
				return false;
			}

			// Are we plus matching?
			if ($plusMatch !== false) {
				// Validate the match
				
			}

			// Cut down the matchDirectory
			$matchDirectory = array_slice($matchDirectory, $match + 1);
		}

		return empty($matchDirectory);
	}

	/**
	 * Match an object against this query
	 * 
	 * @param  PureTree $tree The tree to match
	 * 
	 * @return boolean        The result (true if there was a match)
	 */
	public function match(PureTree $tree) {
		return $this->_query == $tree->name() || $this->_query_path == $tree->path() || $this->matchPath($tree);
	}
}
<?php
/**
 * Purity5 is a HTML5 parser
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace SkylarK\Purity5;

/**
 * The core of Purity5.
 * Usage:
 *   $html = Purity5::parse($markup);
 *   $title = $html("title");
 *   $table_headings = $html("body table tr th");
 *   $active_links = $html("body a[class=active]");
 *   $active_current_links = $html("body a[class=active+current]");
 */
class Purity5
{
	/**
	 * Parse a HTML document and return a query function
	 */
	public static function parse($markup) {
		$parser = new Parser($markup);
		
		return function($query) {
			return "Welcome";
		};
	}
}
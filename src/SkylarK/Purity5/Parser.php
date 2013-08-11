<?php
/**
 * Purity5 is a HTML5 parser
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace SkylarK\Purity5;

use SkylarK\Purity5\DataStructures\PureTree as PureTree;

class Parser
{
	/** Our raw html */
	private $_html;
	/** Our data structure */
	private $_document;

	/**
	 * Construct a new parser
	 */
	public function __construct($html) {
		$this->_html = $html;
		$this->_document = PureTree::buildRoot(array(), '');
	}
}
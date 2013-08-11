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
		$this->parse();
		//$this->_document = PureTree::buildRoot(array(), '');
	}

	/**
	 * Returns the document
	 */
	public function getDocument() {
		return $this->_document;
	}

	/**
	 * Parse the HTML
	 */
	protected function parse() {
		// Generic buffers
		$buffer = '';
		$buffer_in_string = false;

		// Buffers for tag processing
		$buffer_tag = '';
		$buffer_in_tag = false;
		$buffer_tag_name = '';
		$buffer_in_tag_name = false;
		$buffer_last_tag = '';

		// Buffers for attribute parsing
		$buffer_attr = '';
		$buffer_attr_name = '';
		$buffer_attrs = array();

		// Do the split
		$len = strlen($this->_html);
		for ($i = 0; $i < $len; $i++) {
			$chr = $this->_html[$i];
			$buffer .= $chr;

			// Are we starting a new tag?
			if ($chr == '<' && !$buffer_in_string) {
				$buffer_in_tag = true;
				$buffer_in_tag_name = true;
				continue;
			}

			// Should we be appending to a new tag name?
			if ($buffer_in_tag_name) {
				if ($chr == ' ') {
					$buffer_in_tag_name = false;
					continue;
				}
				if ($chr == '>' || $chr == '/') {
					// Close the tag buffer
					if (!$this->_document) {
						$this->_document = PureTree::buildRoot($buffer_tag_name, $buffer_attrs);
					} else {
						$this->_document->createChild($buffer_tag_name, $buffer_attrs);
					}
					$buffer_last_tag = $buffer_tag_name;
					// Reset buffers
					$buffer_in_tag = false;
					$buffer_in_tag_name = false;
					$buffer_tag_name = '';
					$buffer_attrs = array();
					continue;
				}
				$buffer_tag_name .= $chr;
			}
		}
	}
}
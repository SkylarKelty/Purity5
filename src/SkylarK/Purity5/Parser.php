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
		$buffer_parent = $this->_document;
		$buffer_in_string = false;

		// Buffers for tag processing
		$buffer_tag = '';
		$buffer_in_tag = false;
		$buffer_tag_name = '';
		$buffer_in_tag_name = false;
		$buffer_last_tag = '';
		$buffer_tag_closing = false;

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
				// If we hit a space, we are not in a tag anymore
				if ($chr == ' ') {
					$buffer_in_tag_name = false;
					continue;
				}

				// We are closing the last tag
				if ($chr == '/') {
					$buffer_tag_closing = true;
					continue;
				}

				// And we hit the end of the tag
				if ($chr == '>') {
					// If we were closing the previous tag...
					if ($buffer_tag_closing) {
						// Are we expecting this to be closed?
						if ($buffer_tag_name !== $buffer_last_tag) {
							throw new \Exception("Parser error: Wasnt expecting " . $buffer_tag_name . " was expecting " . $buffer_last_tag);
						}

						// We want to go back a bit
						$buffer_parent = $buffer_parent->parent();
						if (!$buffer_parent) {
							$buffer_parent = $this->_document;
						}

						// Set new logic buffers
						$buffer_last_tag = $buffer_parent->name();
						$buffer_tag_closing = false;
					}
					else {
						// We are opening a tag
						if (!$this->_document) {
							$buffer_parent = $this->_document = PureTree::buildRoot($buffer_tag_name, $buffer_attrs);
						}
						else {
							$buffer_parent = $buffer_parent->createChild($buffer_tag_name, $buffer_attrs);
						}
						$buffer_last_tag = $buffer_tag_name;
					}
					
					// Reset buffers
					$buffer_in_tag = false;
					$buffer_in_tag_name = false;
					$buffer_tag_name = '';
					$buffer_attrs = array();

					continue;
				}
				$buffer_tag_name .= $chr;
				continue;
			}

			// Are we in a tag but not in a tag name?
			if ($buffer_in_tag && !$buffer_in_tag_name) {
				// Attribute parsing
				print "\n<<<<<<\n" . substr($this->_html, $i, 10) . "\n";
				$buffer_attrs = $this->parseAttributes($i);
				print substr($this->_html, $i, 10) . "\n\n>>>>>\n";
			}
		}
	}

	/**
	 * Parse tag name
	 */
	private function parseTagName(&$i) {
	}

	/**
	 * Parse attributes
	 */
	private function parseAttributes(&$i) {
		$attributes = array();
		$name_search = true;
		$in_string = false;
		$buffer = '';
		$name = '';
		while (isset($this->_html[$i])) {
			$chr = $this->_html[$i];

			// Increment counter
			$i++;

			// Closing out?
			if (!$in_string && ($chr == '/' || $chr == '>')) {
				$i--;
				break;
			}

			// Is it a space?
			if (!$name_search && !$in_string && $chr == ' ') {
				// Flush buffers?
				$attributes[$name] = $buffer;
				$name_search = true;
				$name = '';
				$buffer = '';
				continue;
			}
			if (!$in_string && $chr == ' ') {
				continue;
			}

			// Is it a quote?
			if ($chr == '\'' || $chr == '"') {
				// Was it escaped?
				if ($i > 0 && $this->_html[$i - 1] !== '\\') {
					// No
					$in_string = !$in_string;
					// Should we flush buffers?
					if (!$in_string) {
						// Yes
						$attributes[$name] = $buffer;
						$name_search = true;
						$name = '';
						$buffer = '';
					}
					continue;
				}
			}

			// Is it an assignment?
			if (!$in_string && $chr == '=') {
				$name_search = false;
				continue;
			}

			// Do we have a name?
			if ($name_search) {
				$name .= $chr;
			} else {
				// Add to buffer
				$buffer .= $chr;
			}
		}

		// Flush buffers
		$attributes[$name] = $buffer;
		return $attributes;
	}
}
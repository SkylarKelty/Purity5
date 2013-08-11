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

			// Have we hit a new tag?
			if ($chr == '<' && !$buffer_in_string) {
				$closing = false;
				$tag_name = $this->parseTagName($i, $closing);
				$tag_attrs = array();
				if (!$closing && $this->_html[$i] == ' ') {
					$tag_attrs = $this->parseAttributes($i);
				}

				// Are we to close something?
				if ($closing) {
					// Are we expecting this to be closed?
					if ($tag_name !== $buffer_last_tag) {
						throw new \Exception("Parser error: Encountered closing " . $tag_name . " was expecting " . $buffer_last_tag);
					}

					// We want to go back a bit
					$buffer_parent = $buffer_parent->parent();
					if (!$buffer_parent) {
						$buffer_parent = $this->_document;
					}
					$buffer_last_tag = $buffer_parent->name();

					continue;
				}

				// Are we to create something?
				if (!$closing) {
					if (!$this->_document) {
						$buffer_parent = $this->_document = PureTree::buildRoot($tag_name, $tag_attrs);
					}
					else {
						$buffer_parent = $buffer_parent->createChild($tag_name, $tag_attrs);
					}
					$buffer_last_tag = $tag_name;
					
					continue;
				}
			}
		}
	}

	/**
	 * Parse tag name
	 */
	private function parseTagName(&$i, &$closing) {
		$buffer = '';

		while (isset($this->_html[$i])) {
			$chr = $this->_html[$i];

			// Finished if we dont have an empty buffer, or have encountered a >
			// Support spaced closing tags, e.g. </ hi  >
			if ((!empty($buffer) && !$closing && $chr == ' ') || $chr == '>') {
				break;
			}

			$i++;

			switch ($chr) {
				case '/':
					$closing = true;
				case ' ':
				case '<':
					continue;
				default:
					$buffer .= $chr;
			}
		}

		return $buffer;
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

			// Closing out?
			if (!$in_string && ($chr == '/' || $chr == '>')) {
				break;
			}

			$i++;

			// Is it a space?
			if (!$in_string && $chr == ' ') {
				// Flush buffers?
				if (!empty($name)) {
					$attributes[$name] = $buffer;
					$name_search = true;
					$name = '';
					$buffer = '';
				}
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
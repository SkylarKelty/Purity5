<?php

class PureTreeTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_SimpleTree() {
		
		$root = new SkylarK\Purity5\DataStructures\PureTree("html", array());

		$this->assertEquals("html", $root->name());
		$this->assertEquals(0, count($root->attributes()));
	}
}
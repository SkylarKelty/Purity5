<?php

use SkylarK\Purity5\DataStructures\PureTree as PureTree;

class PureTreeTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_SimpleTree() {
		
		$root = PureTree::buildRoot('<head><title>Welcome</title></head>');

		$this->assertEquals("html", $root->name());
		$this->assertEquals(0, count($root->attributes()));
		$this->assertEquals('<head><title>Welcome</title></head>', $root->contents());
		$this->assertEquals(0, count($root->children()));
		
		$c1 = $root->createChild("head", array(), '<title>Welcome</title>');
		$c2 = $c1->createChild("title", array(), 'Welcome');


		$this->assertEquals(1, count($root->children()));
		$this->assertEquals(1, count($c1->children()));
		$this->assertEquals(0, count($c2->children()));

		// Now test querying
		$result = $root->query("title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($c2, $result[0]);

		// More complex query
		$result = $root->query("html > head > title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($c2, $result[0]);

		// Another query
		$result = $root->query("html head title");
		//$this->assertEquals(1, count($result));
		//$this->assertEquals($c2, $result[0]);

		// Another query
		$result = $root->query("html title");
		//$this->assertEquals(1, count($result));
		//$this->assertEquals($c2, $result[0]);

		// Another query
		$result = $root->query("head > title");
		//$this->assertEquals(1, count($result));
		//$this->assertEquals($c2, $result[0]);
	}
}
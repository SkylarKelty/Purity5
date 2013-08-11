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
		
		$head = $root->createChild("head", array(), '<title>Welcome</title>');
		$title = $head->createChild("title", array(), 'Welcome');


		$this->assertEquals(1, count($root->children()));
		$this->assertEquals(1, count($head->children()));
		$this->assertEquals(0, count($title->children()));

		$this->assertEquals(1, count($root->path()));
		$this->assertEquals(2, count($head->path()));
		$this->assertEquals(3, count($title->path()));
	}

	public function test_TreeQuery() {
		
		$root = PureTree::buildRoot('<head><title>Welcome</title></head><body><h1>Lorum!</h1><p>String 1</p><p>String 2 <span>Example</span></p></body></html');
		
		$head = $root->createChild("head", array(), '<title>Welcome</title>');
		$title = $head->createChild("title", array(), 'Welcome');

		$body = $root->createChild("body", array(), '<h1>Lorum!</h1><p>String 1</p><p>String 2</p>');
		$h1 = $body->createChild("h1", array(), 'Lorum!');
		$p1 = $body->createChild("p", array(), 'String 1');
		$p2 = $body->createChild("p", array(), 'String 2 <span>Example</span>');
		$span = $p2->createChild("span", array(), 'Example');

		// Now test querying
		$result = $root->query("title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($title, $result[0]);

		// More complex query
		$result = $root->query("html > head > title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($title, $result[0]);

		// Another query
		$result = $root->query("html head title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($title, $result[0]);

		// Another query
		$result = $root->query("html title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($title, $result[0]);

		// Another query
		$result = $root->query("head > title");
		$this->assertEquals(1, count($result));
		$this->assertEquals($title, $result[0]);

		// Test the '>' priorities
		$result = $root->query("body > span");
		$this->assertEquals(0, count($result));
	}
}
<?php

class PureTreeTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_SimpleTree() {
		
		$root = new SkylarK\Purity5\DataStructures\PureTree("html", array(), '<head><title>Welcome</title></head>');

		$this->assertEquals("html", $root->name());
		$this->assertEquals(0, count($root->attributes()));
		$this->assertEquals('<head><title>Welcome</title></head>', $root->contents());
		$this->assertEquals(0, count($root->children()));
		
		$c1 = new SkylarK\Purity5\DataStructures\PureTree("head", array(), '<title>Welcome</title>');
		$root->addChild($c1);
		
		$c2 = new SkylarK\Purity5\DataStructures\PureTree("title", array(), 'Welcome');
		$c1->addChild($c2);


		$this->assertEquals(1, count($root->children()));
		$this->assertEquals(1, count($c1->children()));
		$this->assertEquals(0, count($c2->children()));
	}
}
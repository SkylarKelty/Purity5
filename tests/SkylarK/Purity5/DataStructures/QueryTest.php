<?php

use SkylarK\Purity5\DataStructures\Query as Query;
use SkylarK\Purity5\DataStructures\PureTree as PureTree;

class TestableQuery extends Query
{
	public function call($func, $args = array()) {
		return call_user_func_array(array($this, $func), $args);
	}
}

class QueryTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_BreakPath() {
		$query = new TestableQuery("html");

		$result = $query->call("breakPath", array("html"));
		$this->assertEquals(array("html"), $result);

		$result = $query->call("breakPath", array("html > title"));
		$this->assertEquals(array("html", ">", "title"), $result);

		$result = $query->call("breakPath", array("html title"));
		$this->assertEquals(array("html", "title"), $result);

		$result = $query->call("breakPath", array("html>title"));
		$this->assertEquals(array("html", ">", "title"), $result);

		$result = $query->call("breakPath", array("html> title"));
		$this->assertEquals(array("html", ">", "title"), $result);

		$result = $query->call("breakPath", array("body > h1:first-child"));
		$this->assertEquals(array("body", ">", "h1:first-child"), $result);

		$result = $query->call("breakPath", array("body > h1:nth-child(0)"));
		$this->assertEquals(array("body", ">", "h1:nth-child(0)"), $result);

		$result = $query->call("breakPath", array("html+ title"));
		$this->assertEquals(array("html", "+", "title"), $result);

		$result = $query->call("breakPath", array("html + title"));
		$this->assertEquals(array("html", "+", "title"), $result);
	}

	public function test_MatchPath() {

		$root = PureTree::buildRoot('html', array(), '');
		$head = $root->createChild("head", array(), '');
		$title = $head->createChild("title", array(), '');
		$body = $root->createChild("body", array(), '');
		$h1 = $body->createChild("h1", array(), '');
		$p1 = $body->createChild("p", array(), '');
		$p2 = $body->createChild("p", array(), '');
		$p3 = $p2->createChild("p", array(), '');

		$query = new TestableQuery("html");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);

		$query = new TestableQuery("html title");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);

		$query = new TestableQuery("html > title");
		$result = $query->call("matchPath", array($root));
		$this->assertFalse($result);

		$query = new TestableQuery("html > head");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);

		$query = new TestableQuery("html > head > title");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);

		$query = new TestableQuery("html title");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);

		$query = new TestableQuery("html html");
		$result = $query->call("matchPath", array($root));
		$this->assertFalse($result);

		$query = new TestableQuery("p p");
		$result = $query->call("matchPath", array($root));
		$this->assertTrue($result);
	}
}
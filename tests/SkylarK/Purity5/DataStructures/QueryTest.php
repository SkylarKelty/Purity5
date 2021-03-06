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

		$result = $query->call("breakPath", array("html + title[a+b]"));
		$this->assertEquals(array("html", "+", "title[a+b]"), $result);
	}

	public function test_runPath() {

		$root = PureTree::buildRoot('html', array(), '');
		$head = $root->createChild("head", array(), '');
		$title = $head->createChild("title", array(), '');
		$body = $root->createChild("body", array(), '');
		$h1 = $body->createChild("h1", array("class" => "ipsum"), '');
		$p1 = $body->createChild("p", array("id" => "example"), '');
		$p2 = $body->createChild("p", array("class" => "example lorum"), '');
		$p3 = $p2->createChild("p", array("class" => "lorum"), '');
		$p4 = $p2->createChild("p", array("class" => "ipsum singleton", "data-select" => "me"), '');

		$query = new TestableQuery("html");
		$result = $query->run($root);
		$this->assertEquals(array($root), $result);

		$query = new TestableQuery("p");
		$result = $query->run($root);
		$this->assertEquals(4, count($result));

		$query = new TestableQuery("html title");
		$result = $query->run($root);
		$this->assertEquals(array($title), $result);

		$query = new TestableQuery("html html");
		$result = $query->run($root);
		$this->assertEquals(0, count($result));

		$query = new TestableQuery("p p");
		$result = $query->run($root);
		$this->assertEquals(array($p3, $p4), $result);

		$query = new TestableQuery("html body h1");
		$result = $query->run($root);
		$this->assertEquals(array($h1), $result);

		// Selector magic

		$query = new TestableQuery("html > title");
		$result = $query->run($root);
		$this->assertEquals(0, count($result));

		$query = new TestableQuery("html > head");
		$result = $query->run($root);
		$this->assertEquals(array($head), $result);

		$query = new TestableQuery("html > head > title");
		$result = $query->run($root);
		$this->assertEquals(array($title), $result);

		$query = new TestableQuery("html body h1 + p");
		$result = $query->run($root);
		$this->assertEquals(array($p1), $result);

		$query = new TestableQuery("h1 + p");
		$result = $query->run($root);
		$this->assertEquals(array($p1), $result);

		$query = new TestableQuery("p + p");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p4), $result);

		$query = new TestableQuery("p *");
		$result = $query->run($root);
		$this->assertEquals(array($p3, $p4), $result);

		$query = new TestableQuery("html > head *");
		$result = $query->run($root);
		$this->assertEquals(array($title), $result);

		$query = new TestableQuery("*");
		$result = $query->run($root);
		$this->assertEquals(9, count($result));

		$query = new TestableQuery("#example");
		$result = $query->run($root);
		$this->assertEquals(array($p1), $result);

		$query = new TestableQuery(".example");
		$result = $query->run($root);
		$this->assertEquals(array($p2), $result);

		$query = new TestableQuery(".lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("body .lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("html > body .lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("body > .lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2), $result);

		$query = new TestableQuery("* > .lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("* .lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("p.ipsum");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);

		$query = new TestableQuery("body > h1.ipsum");
		$result = $query->run($root);
		$this->assertEquals(array($h1), $result);

		$query = new TestableQuery("body > p.example.lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2), $result);

		$query = new TestableQuery("p.example.lorum");
		$result = $query->run($root);
		$this->assertEquals(array($p2), $result);

		$query = new TestableQuery("p:first-child");
		$result = $query->run($root);
		$this->assertEquals(array($p3), $result);

		$query = new TestableQuery("p.lorum:first-child");
		$result = $query->run($root);
		$this->assertEquals(array($p3), $result);

		$query = new TestableQuery("body *:first-child");
		$result = $query->run($root);
		$this->assertEquals(array($h1, $p3), $result);

		$query = new TestableQuery("body > *:first-child");
		$result = $query->run($root);
		$this->assertEquals(array($h1), $result);

		$query = new TestableQuery("p:last-child");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p4), $result);

		$query = new TestableQuery("body p:last-child");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p4), $result);

		$query = new TestableQuery("body > p:last-child");
		$result = $query->run($root);
		$this->assertEquals(array($p2), $result);

		$query = new TestableQuery("body *:nth-child(0)");
		$result = $query->run($root);
		$this->assertEquals(array($h1, $p3), $result);

		$query = new TestableQuery("body p:nth-child(1)");
		$result = $query->run($root);
		$this->assertEquals(array($p1, $p4), $result);

		$query = new TestableQuery("body p:nth-child(even)");
		$result = $query->run($root);
		$this->assertEquals(array($p2, $p3), $result);

		$query = new TestableQuery("body p:nth-child(odd)");
		$result = $query->run($root);
		$this->assertEquals(array($p1, $p4), $result);

		$query = new TestableQuery("body p[data-select=me]");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);

		$query = new TestableQuery("p[data-select=me]");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);

		$query = new TestableQuery("p[data-select=me,class=ipsum]");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);

		$query = new TestableQuery("p[class=ipsum+singleton]");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);

		$query = new TestableQuery("p > p[data-select=me,class=ipsum+singleton]:last-child");
		$result = $query->run($root);
		$this->assertEquals(array($p4), $result);
	}
}
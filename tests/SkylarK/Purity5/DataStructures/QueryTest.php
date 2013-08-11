<?php

use SkylarK\Purity5\DataStructures\Query as Query;

class TestableQuery extends Query
{
	public function __construct($query) {
		// -
	}

	public function call($func, $args = array()) {
		return call_user_func_array(array($this, $func), $args);
	}
}

class QueryTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_BreakPath() {
		$query = new TestableQuery("html");

		$result = $query->call("breakPath", array("html > title"));
		$this->assertEquals(array("html", ">", "title"), $result);

		$result = $query->call("breakPath", array("html title"));
		$this->assertEquals(array("html", "title"), $result);

		$result = $query->call("breakPath", array("html>title"));
		$this->assertEquals(array("html", ">", "title"), $result);

		$result = $query->call("breakPath", array("html> title"));
		$this->assertEquals(array("html", ">", "title"), $result);
	}

	public function test_MatchPath() {
		$query = new TestableQuery("html");

		$result = $query->call("matchPath", array(array("html", "title"), array("html", "title")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", "title"), array("html", "head", "title")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", ">", "title"), array("html", "head", "title")));
		$this->assertFalse($result);

		$result = $query->call("matchPath", array(array("html", ">", "head"), array("html", "head", "title")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", "title"), array("root", "second", "html", "title", "level")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", "title"), array("root", "second", "html", "level", "title")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", "html"), array("title", "html", "html")));
		$this->assertTrue($result);

		$result = $query->call("matchPath", array(array("html", "title"), array("title", "html")));
		$this->assertFalse($result);

		$result = $query->call("matchPath", array(array("html", "html"), array("title", "html")));
		$this->assertFalse($result);
	}
}
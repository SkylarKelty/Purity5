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
		$this->assertEquals(array("html", "title"), $result);

		$result = $query->call("breakPath", array("html title"));
		$this->assertEquals(array("html", "title"), $result);

		$result = $query->call("breakPath", array("html>title"));
		$this->assertEquals(array("html", "title"), $result);

		$result = $query->call("breakPath", array("html> title"));
		$this->assertEquals(array("html", "title"), $result);
	}
}
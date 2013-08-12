<?php

class Purity5Test extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_SimpleParse() {
		$html = '<html>
			<head>
				<title>Welcome</title>
			</head>
			<body>
				<h1>Heading</h1>
				<p>Welcome to Purity5!</p>
			</body>
		</html>';
		
		$func = SkylarK\Purity5\Purity5::parse($html);
		$this->assertEquals("Welcome", $func("title"));
		$this->assertEquals("Welcome", $func("head title"));
		$this->assertEquals("Welcome", $func("head")->title);
		$this->assertEquals("Welcome to Purity5!", $func("html> body >p"));
		$this->assertEquals("Welcome to Purity5!", $func("html")->body->p);
		$this->assertEquals("Welcome", $func("title")->contents());
	}

	public function test_Utils() {
		$html = '<html>
			<head>
				<title>Welcome</title>
			</head>
			<body>
				<h1>Heading</h1>
				<p class="welcome" var="true">Welcome to Purity5!</p>
			</body>
		</html>';
		
		$func = SkylarK\Purity5\Purity5::parse($html);
		$this->assertEquals("Welcome to Purity5!", $func(".welcome"));
		$this->assertEquals(array("class" => "welcome", "var" => "true"), $func(".welcome")->attrs());
		$this->assertEquals("welcome", $func(".welcome")->attr("class"));
	}

	public function test_Array() {
		$html = '<html>
			<head>
				<title>Welcome</title>
			</head>
			<body>
				<h1>Heading</h1>
				<p class="welcome" var="true">Welcome to Purity5!</p>
				<p class="lorum" var="true">Lorum Ipsum</p>
			</body>
		</html>';
		
		$func = SkylarK\Purity5\Purity5::parse($html);
		$this->assertEquals(2, count($func("p[var=true]")));
	}
}
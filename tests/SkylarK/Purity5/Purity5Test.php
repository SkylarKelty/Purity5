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
		//$this->assertEquals("Welcome", $func("title"));
		//$this->assertEquals("Welcome", $func("head title"));
		//$this->assertEquals("Welcome", $func("head")->title);
		//$this->assertEquals("Welcome to Purity5!", $func("html> body >p"));
		//$this->assertEquals("Welcome to Purity5!", $func("html")->body->p);
	}
}
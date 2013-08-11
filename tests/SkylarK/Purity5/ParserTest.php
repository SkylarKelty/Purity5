<?php

class ParserTest extends PHPUnit_Framework_TestCase
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
		
		$obj = new SkylarK\Purity5\Parser($html);
		$doc = $obj->getDocument();

		$this->assertEquals("html", $doc->name());
	}
}
<?php

class ParserTest extends PHPUnit_Framework_TestCase
{
	// Tests

	public function test_SimpleParse() {
		$body_contents = '<h1>Heading</h1><p>Welcome to Purity5!</p><img/>';
		$html = '<html>
			<head>
				<title>Welcome</title>
			</head>
			<body>'.$body_contents.'</body>
		</html>';
		$html_contents = substr($html, 6, -7);
		
		$obj = new SkylarK\Purity5\Parser($html);
		$doc = $obj->getDocument();

		// Check the root doc
		$this->assertEquals("html", $doc->name());
		$this->assertEquals($html_contents, $doc->contents());
		$children = $doc->children();
		$this->assertEquals(2, count($children));
		$head = $children[0];
		$this->assertEquals("head", $head->name());
		$body = $children[1];
		$this->assertEquals("body", $body->name());
		$this->assertEquals($body_contents, $body->contents());

		// Check the head
		$head_children = $head->children();
		$this->assertEquals(1, count($head_children));
		$title = $head_children[0];
		$this->assertEquals("title", $title->name());
		$this->assertEquals("Welcome", $title->contents());

		// Check the body
		$body_children = $body->children();
		$this->assertEquals(3, count($body_children));
		$h1 = $body_children[0];
		$this->assertEquals("h1", $h1->name());
		$this->assertEquals("Heading", $h1->contents());
		$p = $body_children[1];
		$this->assertEquals("p", $p->name());
		$this->assertEquals("Welcome to Purity5!", $p->contents());
		$img = $body_children[2];
		$this->assertEquals("img", $img->name());
		$this->assertEquals("", $img->contents());
	}

	public function test_AttributeParse() {
		$html = '<html>
			<head>
				<title>Welcome</title>
			</head>
			<body>
				<h1>Heading</h1>
				<p class="test" data-selector="lorum > ipsum" required foo=bar>Welcome to Purity5!</p>
				<img src="lorum.jpg"/>
			</body>
		</html>';
		
		$obj = new SkylarK\Purity5\Parser($html);
		$doc = $obj->getDocument();

		// Check the root doc
		$this->assertEquals("html", $doc->name());
		$children = $doc->children();
		$this->assertEquals(2, count($children));
		$head = $children[0];
		$this->assertEquals("head", $head->name());
		$body = $children[1];
		$this->assertEquals("body", $body->name());

		// Check the head
		$head_children = $head->children();
		$this->assertEquals(1, count($head_children));
		$title = $head_children[0];
		$this->assertEquals("title", $title->name());

		// Check the body
		$body_children = $body->children();
		$this->assertEquals(3, count($body_children));
		$h1 = $body_children[0];
		$this->assertEquals("h1", $h1->name());
		$p = $body_children[1];
		$this->assertEquals("p", $p->name());
		$this->assertEquals(array("class" => "test", "data-selector" => "lorum > ipsum", "required" => "", "foo" => "bar"), $p->attributes());
		$img = $body_children[2];
		$this->assertEquals("img", $img->name());
		$this->assertEquals(array("src" => "lorum.jpg"), $img->attributes());
	}
}
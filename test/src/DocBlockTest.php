<?php

namespace kamermans\Reflection\Test;

use kamermans\Reflection\DocBlock;

use Number;
use NumberHolder;
use Mathable;
use Add;
use Subtract;

use ReflectionClass;
use ReflectionObject;
use ReflectionMethod;

class DocBlockTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		require_once __DIR__."/../resources/test.php";
	}

	public function testNumberExample() {
		/*
		 * I know this is a worthless test, but hey, it would be nice
		 * to know that the code works, wouldn't it?
		 */
		
		$number = new Number(2);
		$this->assertSame(8, $number->add(10)->subtract(4)->value());
		$this->assertSame("14", (string)$number->add(10)->subtract(4));
	}

	public function testConstructor() {
		// Class
		$doc = new DocBlock(new ReflectionClass("Number"));
		// Object
		$doc = new DocBlock(new ReflectionObject(new Number(1)));
		// Interface
		$doc = new DocBlock(new ReflectionClass("Mathable"));
		// Abstract Class
		$doc = new DocBlock(new ReflectionClass("NumberHolder"));
		// Trait
		$doc = new DocBlock(new ReflectionClass("Add"));

		$num = new Number(1);
		$refl = new ReflectionObject($num);

		// Method
		$doc = new DocBlock($refl->getMethod("value"));
		// Static Method
		$doc = new DocBlock($refl->getMethod("createFromString"));
		// Trait-Imported Method
		$doc = new DocBlock($refl->getMethod("add"));
		// Property
		$doc = new DocBlock($refl->getProperty("number"));
	}

	public function testGetComment() {
		$number_comment = "A Number\nexample:\n<code>\n<?php\n\$number = new Number(2);\necho \$number->add(10)->subtract(4);\n?>\n</code>";
		// Class
		$doc = new DocBlock(new ReflectionClass("Number"));
		$this->assertSame($number_comment, $doc->getComment());
		// Object
		$doc = new DocBlock(new ReflectionObject(new Number(1)));
		$this->assertSame($number_comment, $doc->getComment());
		// Interface
		$doc = new DocBlock(new ReflectionClass("Mathable"));
		$this->assertSame("Supports math operations", $doc->getComment());
		// Abstract Class
		$doc = new DocBlock(new ReflectionClass("NumberHolder"));
		$this->assertSame("Holder of a number", $doc->getComment());
		// Trait
		$doc = new DocBlock(new ReflectionClass("Add"));
		$this->assertSame("Addition", $doc->getComment());

		$num = new Number(1);
		$refl = new ReflectionObject($num);

		// Method
		$doc = new DocBlock($refl->getMethod("value"));
		$this->assertSame("Get the value", $doc->getComment());
		// Static Method
		$doc = new DocBlock($refl->getMethod("createFromString"));
		$this->assertSame("Creates a Number from a string", $doc->getComment());
		// Trait-Imported Method
		$doc = new DocBlock($refl->getMethod("add"));
		$this->assertSame("Adds a number", $doc->getComment());
		// Property
		$doc = new DocBlock($refl->getProperty("number"));
		$this->assertSame("The number", $doc->getComment());
	}

	public function testGetter() {
		// Class
		$doc = new DocBlock(new ReflectionClass("Number"));
		$this->assertSame("v2.1-beta3", $doc->version);
		$this->assertSame([
			"Mathable::add()",
			"Mathable::subtract()",
		], $doc->see);

		// Object
		$doc = new DocBlock(new ReflectionObject(new Number(1)));
		$this->assertSame("v2.1-beta3", $doc->version);

		// Interface
		$doc = new DocBlock(new ReflectionClass("Mathable"));
		$this->assertSame(null, $doc->foo);

		// Abstract Class
		$doc = new DocBlock(new ReflectionClass("NumberHolder"));
		$this->assertSame("Steve Kamerman", $doc->author);
		$this->assertSame("Copyright (c) 2014 The General Public", $doc->copyright);

		// Trait
		$doc = new DocBlock(new ReflectionClass("Subtract"));
		$this->assertSame("", $doc->deprecated);

		$num = new Number(1);
		$refl = new ReflectionObject($num);

		// Method
		$doc = new DocBlock($refl->getMethod("value"));
		$this->assertSame("numeric value", $doc->return);

		$doc = new DocBlock($refl->getMethod("__construct"));
		$this->assertSame("numeric \$number initial value", $doc->param);

		// Static Method
		$doc = new DocBlock($refl->getMethod("compare"));
		$this->assertSame([
			"Number \$a First number",
			"Number \$b Second number",
		], $doc->param);

		// Trait-Imported Method
		$doc = new DocBlock($refl->getMethod("add"));
		$this->assertSame("object this", $doc->return);

		// Property
		$doc = new DocBlock($refl->getProperty("number"));
		$this->assertSame("numeric", $doc->var);
	}

	public function testGetTag() {
		$doc = new DocBlock(new ReflectionClass("Number"));
		$this->assertSame("v2.1-beta3", $doc->getTag("version"));
		$this->assertSame([
			"Mathable::add()",
			"Mathable::subtract()",
		], $doc->getTag("see"));
	}

	public function testGetTags() {
		$doc = new DocBlock(new ReflectionClass("Number"));
		$this->assertSame([
			"version" => "v2.1-beta3",
			"see" => [
				"Mathable::add()",
				"Mathable::subtract()",
			],
		], $doc->getTags());
	}

	public function testTagExists() {
		$doc = new DocBlock(new ReflectionClass("Number"));
		$this->assertTrue($doc->tagExists("see"));
		$this->assertTrue($doc->tagExists("version"));
		$this->assertFalse($doc->tagExists("deprecated"));

		$num = new Number(1);
		$refl = new ReflectionObject($num);

		// Method
		$doc = new DocBlock($refl->getMethod("value"));
		$this->assertTrue($doc->tagExists("return"));

		// Property
		$doc = new DocBlock($refl->getProperty("number"));
		$this->assertTrue($doc->tagExists("var"));

		// Trait-Imported Method
		$doc = new DocBlock($refl->getMethod("subtract"));
		$this->assertTrue($doc->tagExists("deprecated"));
	}

}
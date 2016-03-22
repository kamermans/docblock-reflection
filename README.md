docblock-reflection
===================

[![Build Status](https://travis-ci.org/kamermans/docblock-reflection.svg?branch=master)](https://travis-ci.org/kamermans/docblock-reflection)

Simple, Fast PHP DocBlock Parser / Reflector

This is a dead-simple DocBlock / doc comment / PHPDoc parser.  It separates a block into tags and a comment and that's it.  Nothing fancy here.  If you want fancy, use the Doctrine Annotation bundle.

## Installation ##
Use Composer to install by adding this to your `composer.json`:

```
	"require": {
		"kamermans/docblock-reflection": "~1.0"
	}
```

## Usage ##
This library is basically used for grabbing comments and tags, so here are some examples:

Consider this class
```php
/**
 * A Foo class
 * 
 * @deprecated
 * @version      v1.1
 * @see          Foo::bar()
 * @see          google.com
 */
class Foo {
	/**
	 * Does something that is really
	 * cool and makes your life easy
	 * 
	 * @param string $name Your name
	 * @return string
	 */
	public function bar($name) {
		return "FooBar $name";
	}
}
```

We can explore it using the stock Reflection API, but it doesn't parse the DocBlocks

```php
$reflect = new RelflectionClass("Foo");
echo $reflect->getDocComment(); // spits out the raw block
```

To dig into the comment, use `kamermans\Reflection\DocBlock`.  You can pass anything that implements the `Reflector` interface and has a `getDocComment()` method.  That means `ReflectionObject`, `ReflectionClass`, `ReflectionMethod`, `ReflectionFunction`, etc.

```php
use kamermans\Reflection\DocBlock;

$reflect = new ReflectionClass("Foo");
$doc = new DocBlock($reflect);

// Check if the @deprecated tag exists
$doc->tagExists("deprecated");

// Get the comment "A Foo class"
$doc->getComment();

echo $doc->version; // v1.1

// The same tag can be set multiple times
echo implode("|", $doc->see); // Foo::bar()|google.com

// It works on methods too
$doc = new DocBlock($reflect->getMethod("bar"));
echo "Foo returns a $doc->return\n"; // Foo returns a string

// Multiline comments work too
$doc->getComment();

```
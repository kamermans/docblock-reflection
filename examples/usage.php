<?php

use kamermans\Reflection\DocBlock;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * A Foo class.
 * 
 * @deprecated
 *
 * @version      v1.1
 *
 * @see          Foo::bar()
 * @see          google.com
 */
class Foo
{
    /**
     * Does something that is really
     * cool and makes your life easy.
     * 
     * @param string $name Your name
     *
     * @return string
     */
    public function bar($name)
    {
        return "FooBar $name";
    }
}

$reflect = new ReflectionClass('Foo');
$doc = new DocBlock($reflect);

echo "## Comment ##\n";
echo $doc->getComment() . "\n\n";

echo "## Tags ##\n";
\var_export($doc->getTags());
echo "\n";

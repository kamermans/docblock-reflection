<?php

namespace kamermans\Reflection;

/**
 * A dead-simple PHP DocBlock parser that lets you check for tags and comments
 * in PHP doc block programatically.
 */
class DocBlock
{
    /**
     * @var string
     */
    protected $raw;

    /**
     * @var array
     */
    protected $tags;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @param string|\Reflector $reflectorOrComment
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($reflectorOrComment)
    {
        if (!is_string($reflectorOrComment)) {
            if (method_exists($reflectorOrComment, 'getDocComment')) {
                $reflectorOrComment = $reflectorOrComment->getDocComment();
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Cannot parse DocBlock from an object of type %s.',
                        get_class($reflectorOrComment)
                    )
                );
            }
        }

        $this->parseDocBlock($reflectorOrComment);
    }

    /**
     * Get the raw, unparsed doc comment.
     *
     * @return string
     *
     * @see ReflectionFunctionAbstract::getDocComment()
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Get the comment from the DocBlock.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get an array of tags from the DocBlock.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get a the value for a tag from the DocBlock.
     *
     * @param string $name
     *
     * @return string|array|null
     */
    public function getTag($name, $default = null, $asArray = false)
    {
        if (!isset($this->tags[$name])) {
            return $default;
        }

        return ($asArray && !is_array($this->tags[$name]))
            ? [$this->tags[$name]] : $this->tags[$name];
    }

    /**
     * @param string $name
     *
     * @return string|array|null
     */
    public function __get($name)
    {
        return $this->getTag($name);
    }

    /**
     * Returns true if the tags exists in the DocBlock, even if it has no value.
     *
     * @param string $name
     *
     * @return bool
     */
    public function tagExists($name)
    {
        return array_key_exists($name, $this->tags);
    }

    /**
     * Parses the DocBlock from the raw PHP doc comment.
     *
     * @param string $raw
     */
    protected function parseDocBlock($raw)
    {
        $this->raw = $raw;
        $this->tags = [];
        $raw = str_replace("\r\n", "\n", $raw);
        $lines = explode("\n", $raw);
        $matches = null;

        switch (count($lines)) {
            case 1:
                // handle single-line docblock
                if (!preg_match('#\\/\\*\\*([^*]*)\\*\\/#', $lines[0], $matches)) {
                    return;
                }
                $lines[0] = substr($lines[0], 3, -2);
                break;

            case 2:
                // probably malformed
                return;

            default:
                // handle multi-line docblock
                array_shift($lines);
                array_pop($lines);
                break;
        }

        foreach ($lines as $line) {
            $line = preg_replace('#^[ \t\*]*#', '', $line);

            if (strlen($line) < 2) {
                continue;
            }

            if (preg_match('#@([^ ]+)(.*)#', $line, $matches)) {
                $tag_name = $matches[1];
                $tag_value = trim($matches[2]);

                // If this tag was already parsed, make its value an array
                if (isset($this->tags[$tag_name])) {
                    if (!is_array($this->tags[$tag_name])) {
                        $this->tags[$tag_name] = [$this->tags[$tag_name]];
                    }

                    $this->tags[$tag_name][] = $tag_value;
                } else {
                    $this->tags[$tag_name] = $tag_value;
                }
                continue;
            }

            $this->comment .= "$line\n";
        }

        $this->comment = trim($this->comment);
    }
}

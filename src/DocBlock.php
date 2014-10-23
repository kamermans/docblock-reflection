<?php

namespace kamermans\Reflection;

/**
 * A dead-simple PHP DocBlock parser that lets you check for tags and comments
 * in PHP doc block programatically
 */
class DocBlock {

	protected $raw;
	protected $tags = array();
	protected $comment;

	public function __construct(\Reflector $reflector) {
		if (!method_exists($reflector, "getDocComment")) {
			throw new \InvalidArgumentException("Cannot parse DocBlock from an object of type ".get_class($reflector));
		}
		$this->parseDocBlock($reflector->getDocComment());
	}

	/**
	 * Get the raw, unparsed doc comment
	 * @return string
	 * @see ReflectionFunctionAbstract::getDocComment()
	 */
	public function getRaw() {
		return $this->raw;
	}

	/**
	 * Get the comment from the DocBlock
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}

	/**
	 * Get an array of tags from the DocBlock
	 * @return array
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * Get a the value for a tag from the DocBlock
	 * @return string
	 */
	public function getTag($name) {
		return $this->tagExists($name)? $this->tags[$name]: null;
	}

	public function __get($name) {
		return $this->getTag($name);
	}

	/**
	 * Returns true if the tags exists in the DocBlock, even if it has no value
	 * @return boolean
	 */
	public function tagExists($name) {
		return array_key_exists($name, $this->tags);
	}

	/**
	 * Parses the DocBlock from the raw PHP doc comment
	 * @param  string $raw
	 */
	protected function parseDocBlock($raw) {
		$this->raw = $raw;
		$raw = str_replace("\r\n", "\n", $raw);
		$lines = explode("\n", $raw);

		if (count($lines) < 3) {
			return;
		}

		$start = array_shift($lines);
		$end = array_pop($lines);
		$in_comment = true;

		foreach ($lines as $line) {
			$line = preg_replace('#^[ \t\*]*#', '', $line);

			if (strlen($line) < 2) {
				continue;
			}

			if (preg_match('#@([^ ]+)(.*)#', $line, $matches)) {
				$in_comment = false;
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
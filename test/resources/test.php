<?php
/**
 * Page-level docblock
 */

/**
 * Addition
 * @example
 */
trait Add {
	/**
	 * Adds a number
	 * @param numeric $number
	 * @return object this 
	 */
	public function add($number) {
		$this->number += $number;
		return $this;
	}
}

/**
 * Subtraction
 * @deprecated
 */
trait Subtract {
	/**
	 * Subtracts a number
	 * @param  numeric $number
	 * @return object this
	 * @deprecated
	 */
	public function subtract($number) {
		$this->number -= $number;
		return $this;
	}
}

/**
 * Holder of a number
 * @author 		Steve Kamerman
 * @copyright   Copyright (c) 2014 The General Public
 */
abstract class NumberHolder {
	protected $number;
}

/**
 * Supports math operations
 */
interface Mathable {
	public function add($number);
	public function subtract($number);
}

/**
 * A Number
 *
 * example:
 * <code>
 * <?php
 * $number = new Number(2);
 * echo $number->add(10)->subtract(4);
 * ?>
 * </code>
 * @version    v2.1-beta3
 * @see        Mathable::add()
 * @see        Mathable::subtract()
 */
class Number extends NumberHolder implements Mathable {
	
	use Add;
	use Subtract;

	/**
	 * The number
	 * @var numeric
	 */
	protected $number;

	/**
	 * Constructs a new Number
	 * @param numeric $number initial value
	 */
	public function __construct($number) {
		$this->number = $number;
	}

	/**
	 * Get the value
	 * @return numeric value
	 */
	public function value() {
		return $this->number;
	}

	public function __toString() {
		return (string)$this->value();
	}

	/**
	 * Creates a Number from a string
	 * @param  string $number
	 * @return Number
	 */
	public static function createFromString($number) {
		return new self((float)$number);
	}

	/**
	 * Compare two Numbers
	 * @param  Number $a First number
	 * @param  Number $b Second number
	 * @return int 0 if equal, 1 of $b is greater, -1 if $a is greater
	 */
	public static function compare(Number $a, Number $b) {
		if ($a->value() == $b->value()) return 0;
		return ($b->value() > $a->value())? 1: -1;
	}

}
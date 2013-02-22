<?php
require_once "../lib/math.php";

class MathCalculateTestCase extends PHPUnit_Framework_TestCase {
	
	function testNothing() {
		$this->assertEquals(null, math_calculate(""));
	}

	function testNumber() {
		$this->assertEquals(1, math_calculate("1"));
	}

	function testParenthesisedNumber() {
		$this->assertEquals(1, math_calculate("(1)"));
	}

	function testEmptyParenThrowsException() {
		$this->setExpectedException("Exception");
		math_calculate("()");
	}

	function testUnclosedParenThrowsException() {
		$this->setExpectedException("Exception");
		math_calculate("(");
	}

	function testClosedUnopenedParenThrowsException() {
		$this->setExpectedException("Exception");
		math_calculate(")");
	}

	function testNumberOuterSpacingIgnored() {
		$this->assertEquals(1, math_calculate(" 1 "));
	}

	function testParenthesizedNumberInnerSpacingIgnored() {
		$this->assertEquals(1, math_calculate("( 1 )"));
	}

	function testAdd() {
		$this->assertEquals(3, math_calculate("1 + 2"));
	}

	function testSubtract() {
		$this->assertEquals(-1, math_calculate("1 - 2"));
	}

	function testMultiply() {
		$this->assertEquals(2, math_calculate("1 * 2"));
	}

	function testDivision() {
		$this->assertEquals(0.5, math_calculate("1 / 2"));
	}

	function testExponent() {
		$this->assertEquals(1, math_calculate("1 ^ 2"));
	}

	function testOperatorPrecedence() {
		$this->assertEquals(3066.5, math_calculate("1 / 2 + 3 * 4 ^ 5 - 6"));
	}

	function testParenthesisPrecedence() {
		$this->assertEquals(20, math_calculate("1 / ((2 + 3) * 4) ^ (5 - 6)"));
	}

}
<?php
require_once "../lib/math.php";

class MathLatexFormatterTestCase extends PHPUnit_Framework_TestCase {
	
	function testNothing() {
		$this->assertEquals("", math_latex(""));
	}

	function testNumber() {
		$this->assertEquals("1", math_latex("1"));
	}

	function testParenthesizedNumber() {
		$this->assertEquals("(1)", math_latex("(1)"));
	}

	function testOperatorPrecedence() {
		$this->assertEquals("\\frac{1}{2}+3\\times4^{5}-6", math_latex("1 / 2 + 3 * 4 ^ 5 - 6"));
	}

	function testParenthesisPrecedence() {
		$this->assertEquals("\\frac{1}{((2+3)\\times4)^{(5-6)}}", math_latex("1 / ((2 + 3) * 4) ^ (5 - 6)"));
	}

}
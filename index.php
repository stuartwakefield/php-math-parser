<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

class OperatorToken {
	function execute($a, $b) {}
}

class OperandToken {
	function get() {}
}

class AddOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a + $b;
	}

}

class MultiplyOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a * $b;
	}

}

class DivideOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a / $b;
	}

}

class SubtractOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a - $b;
	}

}

class NumberToken extends OperandToken {

	private $n;

	function __construct($n) {
		$this->n = $n;
	}

	function get() {
		return (int) $this->n;
	}

}

class Operation {

	private $left;
	private $right;
	private $operator;

	function __construct($left, $operator, $right) {
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}

	function run() {
		return $this->operator->execute($this->left->get(), $this->right->get());
	}

}

function token_operator($op) {
	if($op == "+") {
		return new AddOperatorToken();
	} else if($op == "-") {
		return new SubtractOperatorToken();
	} else if($op == "*") {
		return new MultiplyOperatorToken();
	} else if($op == "/") {
		return new DivideOperatorToken();
	}
}

function token_number($n) {
	return new NumberToken($n);
}

function add($a, $b) {
	return $a + $b;
}

function number($a) {
	return (int) $a;
}

// Lexical analysis
function math_lex($expr) {
	$tokens = array();
	$rem = $expr;
	while(strlen($rem) > 0) {
		$rem = trim($rem);

		$matches = array();

		$found = "";

		if(preg_match('/^(\d+)/', $rem, $matches)) {
			$tokens[] = token_number($matches[0]);
		} else if(preg_match('/^([+\/\*-])/', $rem, $matches)) {
			$tokens[] = token_operator($matches[0]);
		}

		if(!count($matches)) die("Lexical error!");

		$rem = substr($rem, strlen($matches[0]));
	}
	return $tokens;
}

// Syntax analysis
function math_syntax($tokens) {
	$tree = null;
	for($i = 0; $i < count($tokens); ++$i) {
		if($tokens[$i] instanceof OperandToken && $tokens[$i + 1] instanceof OperatorToken && $tokens[$i + 2] instanceof OperandToken) {
			$tree = new Operation($tokens[$i], $tokens[$i + 1], $tokens[$i + 2]);
			$i += 2;
		}
	}
	return $tree;
}

function math_evaluate($expr) {
	return math_syntax(math_lex($expr))->run();
}

function assert_equal($a, $b) {
	if($a !== $b) die("Should have been $a but was $b!");
}

// A simple maths expression
assert_equal(4320, math_evaluate("144 * 30"));

echo("Tests pass!");
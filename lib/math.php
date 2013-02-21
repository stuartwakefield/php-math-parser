<?php
// Lexical analysis
function math_lexer($expr) {
	$tokens = array();
	$rem = $expr;
	while(strlen($rem) > 0) {
		$rem = trim($rem);
		$matches = array();
		$token = null;

		if(preg_match('/^(\d*\.\d+)/', $rem, $matches)) {
			$token = array("float", $matches[0]);
		} else if(preg_match('/^(\d+)/', $rem, $matches)) {
			$token = array("integer", $matches[0]);
		} else if(preg_match('/^(\^)/', $rem, $matches)) {
			$token = array("exponent", $matches[0]);
		} else if(preg_match('/^(\*)/', $rem, $matches)) {
			$token = array("multiply", $matches[0]);
		} else if(preg_match('/^(\/)/', $rem, $matches)) {
			$token = array("divide", $matches[0]);
		} else if(preg_match('/^(\+)/', $rem, $matches)) {
			$token = array("add", $matches[0]);
		} else if(preg_match('/^(\-)/', $rem, $matches)) {
			$token = array("subtract", $matches[0]);
		} else if(preg_match('/^\(/', $rem, $matches)) {
			$token = array("openparen", $matches[0]);
		} else if(preg_match('/^\)/', $rem, $matches)) {
			$token = array("closeparen", $matches[0]);
		}

		if(!count($matches)) die("Lexical error!");

		$tokens[] = $token;
		$rem = substr($rem, strlen($matches[0]));
	}
	return $tokens;
}

// Syntax analysis
function math_syntax($tokens) {

	$grammar = array(
		array("number", array("float")),
		array("number", array("integer")),
		array("m", array("multiply")),
		array("m", array("divide")),
		array("a", array("add")),
		array("a", array("subtract")),
		array("paren", array("openparen", "operand", "closeparen")),
		array("op", array("operand", "exponent", "operand")),
		array("op", array("operand", "m", "operand")),
		array("op", array("operand", "a", "operand")),
		array("operand", array("number")),
		array("operand", array("paren")),
		array("operand", array("op"))
	);

	$last = count($tokens);

	while(count($tokens) > 1) {
		$updated = false;

		foreach($grammar as $expr) {
			$pattern = $expr[1];

			for($i = 0; $i <= count($tokens) - count($pattern); ++$i) {
				$match = false;

				for($j = 0; $j < count($pattern); ++$j) {
					$token = $tokens[$i + $j];
					$other = $pattern[$j];
					$match = $token[0] == $other;
					if(!$match) break;
				}

				if($match) {
					$updated = true;
					$group = array_splice($tokens, $i, count($pattern));
					$replace = array($expr[0], $group);
					array_splice($tokens, $i, 0, array($replace));
				}
			}
			
		}

		if(!$updated) {
			var_dump($tokens);
			die("Syntax error! No patterns found!");
		}
	}

	if($tokens[0][0] != "operand") die("Syntax error! Root expression should be operand!");

	return $tokens[0];
}

class Node {
	function get() {

	}
}

class NodeFloat {

	private $value;

	function __construct($value) {
		$this->value = $value;
	}

	function get() {
		return (float) $this->value;
	}
}

class NodeInteger {

	private $value;

	function __construct($value) {
		$this->value = $value;
	}

	function get() {
		return (int) $this->value;
	}

}

class NodeOperation {

	private $left;
	private $right;
	private $operator;

	function __construct($left, $operator, $right) {
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}

	function get() {
		return $this->operator->apply($this->left->get(), $this->right->get());
	}

}

class OperatorExponent {

	function apply($a, $b) {
		return pow($a, $b);
	}

}

class OperatorMultiply {

	function apply($a, $b) {
		return $a * $b;
	}

}

class OperatorDivide {

	function apply($a, $b) {
		return $a / $b;
	}

}

class OperatorAdd {

	function apply($a, $b) {
		return $a + $b;
	}

}

class OperatorSubtract {

	function apply($a, $b) {
		return $a - $b;
	}

}

function math_calculate($tree) {

	$args = array();
	$token = $tree[0];
	$value = $tree[1];

	if(is_array($value)) {
		foreach($value as $child) {
			$args[] = math_calculate($child);
		}
	}

	if($token == "float") {
		$result = new NodeFloat($value);
	} else if($tree[0] == "integer") {
		$result = new NodeInteger($value);
	} else if($tree[0] == "op") {
		$result = new NodeOperation($args[0], $args[1], $args[2]);
	} else if($tree[0] == "exponent") {
		$result = new OperatorExponent();
	} else if($tree[0] == "multiply") {
		$result = new OperatorMultiply();
	} else if($tree[0] == "divide") {
		$result = new OperatorDivide();
	} else if($tree[0] == "add") {
		$result = new OperatorAdd();
	} else if($tree[0] == "subtract") {
		$result = new OperatorSubtract();
	} else if($tree[0] == "paren") {
		$result = $args[1];
	} else if(count($args)) {
		$result = $args[0];
	} else {
		$result = null;
	}

	return $result;
}

function math_evaluate($expr) {
	$tokens = math_lexer($expr);
	$syntax = math_syntax($tokens);
	$result = math_calculate($syntax);
	return $result->get();
}
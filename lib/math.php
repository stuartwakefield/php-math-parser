<?php
require_once "token.php";
require_once "operand_token.php";
require_once "operator_token.php";
require_once "operation.php";

// Lexical analysis
function math_lex($expr) {
	$tokens = array();
	$rem = $expr;
	while(strlen($rem) > 0) {
		$rem = trim($rem);

		$matches = array();

		$found = "";

		
		if(preg_match('/^(\d*\.\d+)/', $rem, $matches)) {
			$tokens[] = token_float($matches[0]);
		} else if(preg_match('/^(\d+)/', $rem, $matches)) {
			$tokens[] = token_integer($matches[0]);
		} else if(preg_match('/^([+\/\*-])/', $rem, $matches)) {
			$tokens[] = token_operator($matches[0]);
		} else if(preg_match('/^\(/', $rem, $matches)) {
			$tokens[] = new OpenParenToken();
		} else if(preg_match('/^\)/', $rem, $matches)) {
			$tokens[] = new CloseParenToken();
		}

		if(!count($matches)) die("Lexical error!");

		$rem = substr($rem, strlen($matches[0]));
	}
	return $tokens;
}

// FIXME Pointless classes
class OpenParenToken {

}

class CloseParenToken {

}

class Paren extends Operand {

	private $inner;

	function __construct($left, $inner, $right) {
		$this->inner = $inner;
	}

	function get() {
		return $this->inner->get();
	}

}

// Syntax analysis
function math_syntax($tokens) {

	$patterns = array(
		array("Paren", array("OpenParenToken", "Operand", "CloseParenToken")),
		array("Operation", array("Operand", "ExponentOperatorToken", "Operand")),
		array("Operation", array("Operand", "MultiplyOperatorToken", "Operand")),
		array("Operation", array("Operand", "AddOperatorToken", "Operand"))
	);

	$last = count($tokens);

	while(count($tokens) > 1) {
		foreach($patterns as $pattern) {
			$pattern_tokens = $pattern[1];
			$len = count($tokens) - count($pattern[1]);

			if($len >= 0) {
				for($i = 0; $i <= $len; ++$i) {
					$match = false;
					for($j = 0; $j < count($pattern_tokens); ++$j) {
						$match = ($tokens[$i + $j] instanceof $pattern_tokens[$j]);
						if(!$match) {
							break;
						}
					}
					if($match) {
						// FIXME... Hardcoded length
						$grouped = new $pattern[0]($tokens[$i], $tokens[$i + 1], $tokens[$i + 2]);
						array_splice($tokens, $i, count($pattern[1]), array($grouped));
						$len -= 2;
					}
				}
			}
		}
		// This pattern matching is pretty rubbish... FIXME!

		if(count($tokens) === $last) die("Syntax error!");

	}

	if(!($tokens[0] instanceof Operand)) die("Syntax error!");

	return $tokens[0];
}

function math_evaluate($expr) {
	return math_syntax(math_lex($expr))->get();
}
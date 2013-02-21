<?php
require_once "token.php";
require_once "tokens/operand_token.php";
require_once "tokens/operator_token.php";
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
		}

		if(!count($matches)) die("Lexical error!");

		$rem = substr($rem, strlen($matches[0]));
	}
	return $tokens;
}

// Syntax analysis
function math_syntax($tokens) {
	$tree = null;

	// This pattern matching is pretty rubbish... FIXME!
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
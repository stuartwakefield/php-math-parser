<?php
require_once "tokens/add_operator_token.php";
require_once "tokens/subtract_operator_token.php";
require_once "tokens/multiply_operator_token.php";
require_once "tokens/divide_operator_token.php";
require_once "tokens/number_token.php";

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
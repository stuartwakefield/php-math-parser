<?php
require_once "add_operator_token.php";
require_once "subtract_operator_token.php";
require_once "multiply_operator_token.php";
require_once "divide_operator_token.php";
require_once "integer_token.php";
require_once "float_token.php";

function token_operator($op) {
	if($op == "+") {
		return new AddOperatorToken();
	} else if($op == "-") {
		return new SubtractOperatorToken();
	} else if($op == "*") {
		return new MultiplyOperatorToken();
	} else if($op == "/") {
		return new DivideOperatorToken();
	} else if($op == "^") {

	}
}

function token_integer($n) {
	return new IntegerToken($n);
}

function token_float($n) {
	return new FloatToken($n);
}
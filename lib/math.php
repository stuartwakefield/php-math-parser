<?php
require_once "parse.php";
require_once "calculate/node_float.php";
require_once "calculate/node_integer.php";
require_once "calculate/node_operation.php";
require_once "calculate/operator_exponent.php";
require_once "calculate/operator_multiply.php";
require_once "calculate/operator_divide.php";
require_once "calculate/operator_add.php";
require_once "calculate/operator_subtract.php";

function math_calculate_tree_on_float($val) {
	return new NodeFloat($val);
}

function math_calculate_tree_on_integer($val) {
	return new NodeInteger($val);
}

function math_calculate_tree_on_exponent($val) {
	return new OperatorExponent();
}

function math_calculate_tree_on_multiply($val) {
	return new OperatorMultiply();
}

function math_calculate_tree_on_divide($val) {
	return new OperatorDivide();
}

function math_calculate_tree_on_add($val) {
	return new OperatorAdd();
}

function math_calculate_tree_on_subtract($val) {
	return new OperatorSubtract();
}

function math_calculate_tree_on_operation($val) {
	return new NodeOperation($val[0], $val[1], $val[2]);
}

function math_calculate_tree_on_wrapper($val) {
	return $val[0];
}

function math_calculate_tree_on_paren($val) {
	return $val[1];
}

function math_calculate_tree_on_stub($val) {
	return null;
}

function math_calculate($expr) {

	if(!strlen(trim($expr))) 
		return null;

	$tokens = parse_tokens($expr, array(
		array('(\d*\.\d+)', "float"),
		array('(\d+)', "integer"),
		array('(\^)', "exponent"),
		array('(\*)', "multiply"),
		array('(\/)', "divide"),
		array('(\+)', "add"),
		array('(\-)', "subtract"),
		array('\(', "openparen"),
		array('\)', "closeparen")
	));

	if(!count($tokens))
		throw new Exception("Math expression invalid! No tokens found!");

	$tree = parse_grammar($tokens, array(
		array("n", array("float")),
		array("n", array("integer")),
		array("m", array("multiply")),
		array("m", array("divide")),
		array("a", array("add")),
		array("a", array("subtract")),
		array("p", array("openparen", "o", "closeparen")),
		array("op", array("o", "exponent", "o")),
		array("op", array("o", "m", "o")),
		array("op", array("o", "a", "o")),
		array("o", array("n")),
		array("o", array("p")),
		array("o", array("op"))
	));
	
	if($tree[0] != "o")
		throw new Exception("Math expression invalid! Root should be operand!");

	$result = parse_traverse($tree, array(
		"float" => "math_calculate_tree_on_float",
		"integer" => "math_calculate_tree_on_integer",
		"exponent" => "math_calculate_tree_on_exponent",
		"multiply" => "math_calculate_tree_on_multiply",
		"divide" => "math_calculate_tree_on_divide",
		"add" => "math_calculate_tree_on_add",
		"subtract" => "math_calculate_tree_on_subtract",
		"op" => "math_calculate_tree_on_operation",
		"o" => "math_calculate_tree_on_wrapper",
		"m" => "math_calculate_tree_on_wrapper",
		"a" => "math_calculate_tree_on_wrapper",
		"n" => "math_calculate_tree_on_wrapper",
		"p" => "math_calculate_tree_on_paren",
		"openparen" => "math_calculate_tree_on_stub",
		"closeparen" => "math_calculate_tree_on_stub"
	));

	if($result == null)
		throw new Exception("Math expression invalid!");

	return $result->get();
}
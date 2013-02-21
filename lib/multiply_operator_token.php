<?php
require_once "operator_token.php";

class MultiplyOperatorToken implements OperatorToken {
	
	function execute($a, $b) {
		return $a * $b;
	}

}
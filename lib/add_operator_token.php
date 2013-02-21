<?php
require_once "operator_token.php";

class AddOperatorToken implements OperatorToken {
	
	function execute($a, $b) {
		return $a + $b;
	}

}
<?php
require_once "operator_token.php";

class AddOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a + $b;
	}

}
<?php
require_once "operator_token.php";

class SubtractOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a - $b;
	}

}
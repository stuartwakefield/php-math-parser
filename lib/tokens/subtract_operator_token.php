<?php
require_once "operator_token.php";

class SubtractOperatorToken implements OperatorToken {
	
	function execute($a, $b) {
		return $a - $b;
	}

}
<?php
require_once "operator_token.php";

class DivideOperatorToken implements OperatorToken {
	
	function execute($a, $b) {
		return $a / $b;
	}

}
<?php
require_once "operator_token.php";

class DivideOperatorToken extends MultiplyOperatorToken {
	
	function execute($a, $b) {
		return $a / $b;
	}

}
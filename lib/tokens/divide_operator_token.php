<?php
require_once "operator_token.php";

class DivideOperatorToken extends OperatorToken {
	
	function execute($a, $b) {
		return $a / $b;
	}

}
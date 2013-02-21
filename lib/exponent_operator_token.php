<?php
require_once "operator_token.php";

class ExponentOperatorToken implements OperatorToken {
	
	function execute($a, $b) {
		return pow($a, $b);
	}

}
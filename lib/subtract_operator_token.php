<?php
require_once "operator_token.php";

class SubtractOperatorToken extends AddOperatorToken {
	
	function execute($a, $b) {
		return parent::execute($a, -$b);
	}

}
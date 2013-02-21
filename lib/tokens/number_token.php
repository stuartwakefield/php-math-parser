<?php
require_once "operand_token.php";

class NumberToken extends OperandToken {

	private $n;

	function __construct($n) {
		$this->n = $n;
	}

	function get() {
		return (int) $this->n;
	}

}
<?php
require_once "operand_token.php";

class FloatToken implements OperandToken {

	private $n;

	function __construct($n) {
		$this->n = $n;
	}

	function get() {
		return (float) $this->n;
	}

}
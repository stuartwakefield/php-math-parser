<?php
require_once "operand_token.php";
require_once "operand.php";

class FloatToken extends Operand implements OperandToken {

	private $n;

	function __construct($n) {
		$this->n = $n;
	}

	function get() {
		return (float) $this->n;
	}

}
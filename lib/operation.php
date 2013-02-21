<?php
require_once "operand.php";

class Operation extends Operand {

	private $left;
	private $right;
	private $operator;

	function __construct($left, $operator, $right) {
		$this->left = $left;
		$this->right = $right;
		$this->operator = $operator;
	}

	function get() {
		return $this->operator->execute($this->left->get(), $this->right->get());
	}

}
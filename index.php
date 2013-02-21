<?php
require_once "lib/math.php";
require_once "lib/assert.php";

// Simple maths expression (operand operator operand)
assert_equal(4320, math_evaluate("144 * 30"));
assert_equal(4, math_evaluate("12 / 3"));
assert_equal(7565, math_evaluate("5425 + 2140"));
assert_equal(95, math_evaluate("142 - 47"));

echo("Tests pass!");
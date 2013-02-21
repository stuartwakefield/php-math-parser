<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "lib/math.php";
require_once "lib/assert.php";

// Simple maths expression (operand operator operand)
assert_equal(4320, math_evaluate("144 * 30"));
assert_equal(4, math_evaluate("12 / 3"));
assert_equal(7565, math_evaluate("5425 + 2140"));
assert_equal(95, math_evaluate("142 - 47"));

// With decimal point
assert_floaty_equal(43.2, math_evaluate("1.44 * 30"));
assert_floaty_equal(0.4, math_evaluate("1.2 / 3"));
assert_floaty_equal(75.65, math_evaluate("54.25 + 21.4"));
assert_floaty_equal(9.5, math_evaluate("14.2 - 4.7"));

// assert_equal(2, math_evaluate("(2 * (1 + 2)) / 3"));

echo("Tests pass!");
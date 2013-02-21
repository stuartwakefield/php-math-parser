<?php
// The built in assert isn't what I want
function assert_equal($a, $b) {
	if($a !== $b) die("Should have been $a but was $b!");
}

function assert_floaty_equal($a, $b) {
	if(abs($a - $b) > 0.000001) die("Should have been $a but was $b!");
}
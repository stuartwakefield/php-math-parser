<?php
// The built in assert isn't what I want
function assert_equal($a, $b) {
	if($a !== $b) die("Should have been $a but was $b!");
}
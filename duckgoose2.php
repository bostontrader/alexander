<?php


/**
 * The purpose of this code is to implement a solution for Problem #1 of the Problem Statement,
 * suitable refactored to enable testing, as well as to include some rudimentary testing.
 */

for($i = 1; $i <=100; $i++) {
    print duckNGooser($i)."\n";
}

// Invoke some tests
$error_cnt = 0;
$error_cnt += tester(1, 1);
$error_cnt += tester(3, "Duck");
$error_cnt += tester(5, "Goose");
$error_cnt += tester(15, "DuckGoose");
$error_cnt += tester(100, "Goose");

if($error_cnt == 0) {print "Tests complete. No errors.";}

/**
 * This test function will submit an integer to the duckNGooser and compare the result with an expected value.
 * @param $n  What integer to test.
 * @param $expected Expected result.
 *
 * If the test passes, do not print any message and return 0.
 * If the test failes, print an error message and return 1
 *
 */
function tester($n, $expected) {
  $result = duckNGooser($n);
  if($result != $expected) {
    print "Error: duckNGooser($n) sb $expected, but instead it is $result\n";
    return 1;
  }
  return 0;
}

/**
 * Given an integer $n, from 1 to 100 inclusive:
 * ... if $n is a multiple of 3 but not 5, return "Duck"
 * ... if $n is a multiple of 5 but not 3, return "Goose"
 * ... if $n is a multiple of 3 and 5, return "DuckGoose"
 * ... for any other $n, return $n
 */
function duckNGooser($n) {

    $isMultipleOf3 = $n % 3 == 0;
    $isMultipleOf5 = $n % 5 == 0;

    if($isMultipleOf3 && $isMultipleOf5) {
        return "DuckGoose";
    } else if($isMultipleOf3) {
        return "Duck";
    } else if($isMultipleOf5) {
        return "Goose";
    } else {
        return $n;
    }
}

?>

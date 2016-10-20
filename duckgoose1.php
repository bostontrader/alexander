<?php

/**
 * The purpose of this code is to implement a solution for Problem #1 of the Problem Statement.
 * Where the problem statement says “numbers” this code assumes the meaning to be “integers”.
 *
 * The problem statement does not mention anything about parameter checking or error handling. I therefore ignore
 * those issues.
 */

for($i = 1; $i <=100; $i++) {

  $isMultipleOf3 = $i % 3 == 0;
  $isMultipleOf5 = $i % 5 == 0;

  if($isMultipleOf3 && $isMultipleOf5) {
    print "DuckGoose\n";
  } else if($isMultipleOf3) {
    print "Duck\n";
  } else if($isMultipleOf5) {
    print "Goose\n";
  } else {
    print "$i\n";
  }
}

?>

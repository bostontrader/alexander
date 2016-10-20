<?php

/**
 * The purpose of this code is to implement a solution for Problem #2 of the Problem Statement.
 *
 * The problem statement does not specify any display, printing, or logging of any results so I do not do
 * any of that. I do however have some testing and if the testing finds any problems, it will squeal.
 *
 * The problem statement does not mention anything about parameter checking or error handling. I therefore ignore
 * those issues.
 */

$data=[
  [
    'name'=>'Julie',
    'key'=>'64489c85dc2fe0787b85cd87214b3810',
    'age'=>20
  ],

  [
    'name'=>'Martin',
    'key'=>'bb07c989b57c25fd7e53395c3e118185',
    'age'=>18
  ],

  [
    'name'=>'Lucy',
    'key'=>'ab3aec6d954571c7551a186ea1cd98ff',
    'age'=>100
  ],

  [
    'name'=>'Jessica',
    'age'=>25,
    'key'=>'e1a118c9178aa3538f39a9c8131938ed'
  ],
];

/**
 * Sort and then verify the six different permutations of sort column and sort order.
 * The problem statement implicitly requires the $data to sort to be available globally.
 * The sorting will sort the actual input array, it won't create a new array.
 */
$error_cnt = 0;
$error_cnt += sortAndVerify('age',  'asc');
$error_cnt += sortAndVerify('age',  'desc');
$error_cnt += sortAndVerify('name', 'asc');
$error_cnt += sortAndVerify('name', 'desc');
$error_cnt += sortAndVerify('key',  'asc');
$error_cnt += sortAndVerify('key',  'desc');
if($error_cnt == 0) {print "Tests complete. No errors.";}

/**
 * This function will sort the $data array, on $col, in $order and will then verify
 * that $data is sorted correctly.
 * @param $col
 * @param $order
 * @return 0 if $data can be verified to be sorted correctly, else 1.
 */
function sortAndVerify($col, $order) {
  sortData($col, $order);
  return verifySort($col, $order);
}

/**
 * This function will sort the $data array, on $col, in $order.
 * @param $col
 * @param $order
 *
 * No return value.
 */
function sortData($col, $order) {

  global $data;

  usort($data, function ($a, $b) use ($col, $order) {
    if($order === 'asc')
      return strnatcmp($a[$col], $b[$col]);
    else if($order === 'desc')
      return strnatcmp($b[$col], $a[$col]);
    else
      return 0;
  });
  //var_dump($data);
}

/**
 * This function will verify that the $data array is correctly sorted on $col, in $order.
 * @param $col
 * @param $order
 *
 * Return 0 if $data is sorted correctly, else 1.
 */
function verifySort($col, $order) {

  global $data;

  $priorRecord = null;
  foreach($data as $key => $value) {
    if($priorRecord === null) {
      // No prior record to compare. This is the first one.
      $priorRecord = $value;
    } else {
      $n1 = $priorRecord[$col];
      $n2 = $value[$col];
      if($order === 'asc' && $n1 > $n2) { // for asc, $n1 sb <= $n2
        printErrorMessage($col, $order, $priorRecord, $value);
        return 1;
      } else if ($order === 'desc' && $n2 > $n1) { // for desc, $n2 sb <= $n1
        printErrorMessage($col, $order, $priorRecord, $value);
        return 1;
      }
    }
  }
  return 0;
}

/**
 * This function will print an error message for verifySort. It's called from two different
 * locations, hence its factored out here.
 *
 * @param $col
 * @param $order
 * @param @priorRecord
 * @param @currentRecord
 *
 * No return value.
 */
function printErrorMessage($col, $order, $priorRecord, $currentRecord) {
  print "Error: Sorting on column $col, $order\n";
  print "Prior record = \n";
  var_dump($priorRecord);
  print "Current record = \n";
  var_dump($currentRecord);
}

?>

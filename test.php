<?php
// Copyright 2018 Dave Diamond
// See LICENSE

include "rel2abs.php";

// NOTE: tests originally validated using GO's url.ResolveReference
// method.

$BASE = "*****";

$testCount = 0;

// test group #1

$bases = array(
  "http://host:80/x/y/z.html",
  "http://host:80/x/y/");

$tests = array(
  (object)array("rel" => "", "abs" => $BASE),
  (object)array("rel" => "https://foo.bar:9000/a/b/c.html", "abs" => "https://foo.bar:9000/a/b/c.html"),
  (object)array("rel" => "//foo.bar/a/b/c", "abs" => "http://foo.bar/a/b/c"),
  (object)array("rel" => "/a/b/c.php", "abs" => "http://host:80/a/b/c.php"),
  (object)array("rel" => "a/b/c", "abs" => "http://host:80/x/y/a/b/c"),
  (object)array("rel" => "../a/b/c", "abs" => "http://host:80/x/a/b/c"),
  (object)array("rel" => "../../a/b/c", "abs" => "http://host:80/a/b/c"),
  (object)array("rel" => "../../../a/b/c", "abs" => "http://host:80/a/b/c"),
  (object)array("rel" => ".txt", "abs" => "http://host:80/x/y/.txt"),
  (object)array("rel" => "/q", "abs" => "http://host:80/q"),
  (object)array("rel" => "relative.php", "abs" => "http://host:80/x/y/relative.php"),
  (object)array("rel" => "/absolute1.php", "abs" => "http://host:80/absolute1.php"),
  (object)array("rel" => "./relative.php", "abs" => "http://host:80/x/y/relative.php"),
  (object)array("rel" => "../relative.php", "abs" => "http://host:80/x/relative.php"),
  (object)array("rel" => "//absolutedomain.org", "abs" => "http://absolutedomain.org"),
  (object)array("rel" => ".", "abs" => "http://host:80/x/y/"),
  (object)array("rel" => "..", "abs" => "http://host:80/x/"),
  (object)array("rel" => "../", "abs" => "http://host:80/x/"),
  (object)array("rel" => "./", "abs" => "http://host:80/x/y/"),
);

for ($j = 0; $j < count($bases); $j++) {
  $base = $bases[$j];
  for ($i = 0; $i < count($tests); $i++) {
    $test = $tests[$i];
    $expect = $test->abs;
    if ($expect == $BASE) $expect = $base;
    $actual = rel2abs($base, $test->rel);
    if ($actual != $expect) {
      echo "test #1.{$j}.{$i} failed - expected {$expect}, rel2abs returned {$actual}\n";
      exit(-1);
    }
    $testCount++;
  }
}

// test group #2

$tests = array(
  (object)array("base" => "http://host:80//x//y//", "rel" => "", "abs" => "http://host:80/x//y//"),
  (object)array("base" => "http://host:80//x//y//", "rel" => ".", "abs" => "http://host:80/x//y//"),
  (object)array("base" => "http://host:80//x//y//", "rel" => "..", "abs" => "http://host:80/x//y/"),
  (object)array("base" => "http://host:80//x//y//", "rel" => "../..", "abs" => "http://host:80/x//"),
  (object)array("base" => "http://host:80//x//y//", "rel" => "../../..", "abs" => "http://host:80/x/"),
  (object)array("base" => "http://host:80//x//y//", "rel" => "../../../..", "abs" => "http://host:80/"),
  (object)array("base" => "http://host:80//x//y//", "rel" => "../../../../..", "abs" => "http://host:80/"),
  (object)array("base" => "", "rel" => "", "abs" => ""),
);

for ($i = 0; $i < count($tests); $i++) {
  $test = $tests[$i];
  $expect = $test->abs;
  $actual = rel2abs($test->base, $test->rel);
  if ($actual != $expect) {
    echo "test #2.{$i} failed - expected {$expect}, rel2abs returned {$actual}\n";
    exit(-1);
  }
  $testCount++;
}

// test group #3

$base = "http://host:80/x/y/z";

$tests = array(
  "?q=w",
  "#mark",
);

for ($i = 0; $i < count($tests); $i++) {
  $test = $tests[$i];
  $expect = $base + $test;
  $actual = rel2abs($base, $test);
  if ($actual != $expect) {
    echo "test #3.{$i} failed - expected {$expect}, rel2abs returned {$actual}\n";
    exit(-1);
  }
  $testCount++;
}

// make sure we hit all the tests
$expect = 19*2 + 8 + 2;
if ($testCount != $expect) {
  echo "wrong number of tests ran - expected {$expect} but {$testCount} ran.\n";
  exit(-1);
}

// all's well that ends well.
echo "all {$expect} tests ran correctly!\n";
exit(0);

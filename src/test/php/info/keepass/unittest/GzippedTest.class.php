<?php namespace info\keepass\unittest;

use info\keepass\Gzipped;
use lang\FormatException;

class GzippedTest extends \unittest\TestCase {

  #[@test]
  public function gzencoded_test() {
    new Gzipped("\037\213\b\000\000\000\000\000\000\v\vI-.\001\0002\321Mx\004\000\000\000");
  }

  #[@test, @expect(FormatException::class)]
  public function empty_input_is_invalid() {
    new Gzipped('');
  }
}
